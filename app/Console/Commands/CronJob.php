<?php

namespace App\Console\Commands;

use App\Events\Invoice\Created as InvoiceCreated;
use App\Events\Invoice\Reminder as InvoiceReminder;
use App\Jobs\Server\SuspendJob;
use App\Jobs\Server\TerminateJob;
use App\Models\EmailLog;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cron-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run automated tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Send invoices if due date is x days away
        $sendedInvoices = 0;
        Service::where('status', 'active')->where('expires_at', '<', now()->addDays((int) config('settings.cronjob_invoice')))->get()->each(function ($service) use (&$sendedInvoices) {
            // Does the service have already a pending invoice?
            if ($service->invoices()->where('status', 'pending')->exists() || $service->cancellation()->exists()) {
                return;
            }

            // Calculate if we should edit the price because of the coupon
            if ($service->coupon) {
                // Calculate what iteration of the coupon we are in
                $iteration = $service->invoices()->count() + 1;
                if ($iteration == $service->coupon->recurring) {
                    // Calculate the price
                    $price = $service->plan->prices()->where('currency_code', $service->currency_code)->first()->price;
                    $service->price = $price;
                    $service->save();
                }
            }

            // Create invoice
            $invoice = $service->invoices()->make([
                'user_id' => $service->order->user_id,
                'status' => 'pending',
                'due_at' => $service->expires_at,
                'currency_code' => $service->currency_code,
            ]);

            $invoice->saveQuietly();
            // Create invoice items
            $invoice->items()->create([
                'reference_id' => $service->id,
                'reference_type' => Service::class,
                'price' => $service->price,
                'quantity' => $service->quantity,
                'description' => $service->description,
            ]);

            event(new InvoiceCreated($invoice));

            $sendedInvoices++;
        });
        $this->info('Sending invoices if due date is ' . config('settings.cronjob_invoice') . ' days away: ' . $sendedInvoices . ' invoices');

        // Send invoice reminders if due date is x days away
        $reminderInvoices = 0;
        Service::where('status', 'active')->whereHas('invoices', function ($query) {
                $query->where('status', 'pending');
        })->where('expires_at', '=', now()->addDays((int) config('settings.cronjob_invoice_reminder'))->toDateString())->whereDoesntHave('cancellation')->get()->each(function ($service) use (&$reminderInvoices) {
                $invoice = $service->invoices()->where('status', 'pending')->first();
                if ($invoice) {
                        event(new InvoiceReminder($invoice));
                        $reminderInvoices++;
                }
        });
        $this->info('Sending invoice reminders for services expiring in ' . config('settings.cronjob_invoice_reminder') . ' days: ' . $reminderInvoices . ' reminders');

        // Cancel services if first invoice is not paid after x days
        $ordersCancelled = 0;
        Service::where('status', 'pending')->whereDoesntHave('invoices', function ($query) {
            $query->where('status', 'paid');
        })->where('created_at', '<', now()->subDays((int) config('settings.cronjob_order_cancel')))->get()->each(function ($service) use (&$ordersCancelled) {
            $service->update(['status' => 'cancelled']);

            $ordersCancelled++;
        });
        $this->info('Cancelling services if first invoice is not paid after ' . config('settings.cronjob_order_cancel') . ' days: ' . $ordersCancelled . ' orders');

        $updatedUpgradeInvoices = 0;
        ServiceUpgrade::where('status', 'pending')->get()->each(function ($upgrade) use (&$updatedUpgradeInvoices) {
            if ($upgrade->service->expires_at < now()) {
                $upgrade->update(['status' => 'cancelled']);
                $upgrade->invoice->update(['status' => 'cancelled']);

                $updatedUpgradeInvoices++;

                return;
            }

            $upgrade->invoice->items()->update([
                'price' => $upgrade->calculatePrice()->price,
            ]);

            $updatedUpgradeInvoices++;
        });

        // Suspend orders if due date is overdue for x days
        $ordersSuspended = 0;
        Service::where('status', 'active')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_order_suspend')))->each(function ($service) use (&$ordersSuspended) {
            SuspendJob::dispatch($service);

            $service->update(['status' => 'suspended']);
            $ordersSuspended++;
        });
        $this->info('Suspending orders if due date is overdue for ' . config('settings.cronjob_order_suspend') . ' days: ' . $ordersSuspended . ' orders');

        // Terminate orders if due date is overdue for x days
        $ordersTerminated = 0;
        Service::where('status', 'suspended')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_order_terminate')))->each(function ($service) use (&$ordersTerminated) {
            TerminateJob::dispatch($service);
            $service->update(['status' => 'cancelled']);
            // Cancel outstanding invoices
            $service->invoices()->where('status', 'pending')->update(['status' => 'cancelled']);
            $ordersTerminated++;
        });
        $this->info('Terminating orders if due date is overdue for ' . config('settings.cronjob_order_terminate') . ' days: ' . $ordersTerminated . ' orders');

        // Close tickets if no response for x days
        $ticketClosed = 0;
        Ticket::where('status', 'replied')->each(function ($ticket) use (&$ticketClosed) {
            $lastMessage = $ticket->messages()->latest('created_at')->first();
            if ($lastMessage && $lastMessage->created_at < now()->subDays((int) config('settings.cronjob_close_ticket'))) {
                $ticket->update(['status' => 'closed']);
                $ticketClosed++;
            }
        });
        $this->info('Closing tickets if no response for ' . config('settings.cronjob_close_ticket') . ' days: ' . $ticketClosed . ' tickets');

        // Delete email logs older then x
        $this->info('Deleting email logs older then ' . config('settings.cronjob_delete_email_logs') . ' days: ' . EmailLog::where('created_at', '<', now()->subDays((int) config('settings.cronjob_delete_email_logs')))->count());
        EmailLog::where('created_at', '<', now()->subDays((int) config('settings.cronjob_delete_email_logs')))->delete();

        // Check for updates
        $this->info('Checking for updates...');

        if (config('app.version') == 'development') {
            $this->info('You are using the development version. No update check available.');

            return;
        } elseif (config('app.version') == 'beta') {
            // Check if app.commit is different from the latest commit
            $latestCommit = Http::get('https://api.github.com/repos/Paymenter/Paymenter/commits')->json()[0]['sha'];
            if (config('app.commit') != $latestCommit) {
                $this->info('A new version is available: ' . config('app.commit'));
            } else {
                $this->info('You are using the latest version: ' . config('app.commit'));
            }
        } else {
            // Check if app.version is different from the latest version
            $latestVersion = Http::get('https://api.github.com/repos/Paymenter/Paymenter/releases/latest')->json()['tag_name'];
            if (config('app.version') != $latestVersion) {
                $this->info('A new version is available: ' . $latestVersion);
            } else {
                $this->info('You are using the latest version: ' . config('app.version'));
            }
        }
    }
}
