<?php

namespace App\Console\Commands;

use App\Helpers\ExtensionHelper;
use App\Jobs\Server\SuspendJob;
use App\Jobs\Server\TerminateJob;
use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\Ticket;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

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
        Config::set('audit.console', true);

        // Send invoices if due date is x days away
        $sendedInvoices = 0;
        Service::where('status', 'active')->where('expires_at', '<', now()->addDays((int) config('settings.cronjob_invoice', 7)))->get()->each(function ($service) use (&$sendedInvoices) {
            // Does the service have already a pending invoice?
            if ($service->invoices()->where('status', 'pending')->exists() || $service->cancellation()->exists()) {
                return;
            }

            DB::beginTransaction();

            try {
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
                    'user_id' => $service->user_id,
                    'status' => 'pending',
                    'due_at' => $service->expires_at,
                    'currency_code' => $service->currency_code,
                ]);

                $invoice->save();
                // Create invoice items
                $invoice->items()->create([
                    'reference_id' => $service->id,
                    'reference_type' => Service::class,
                    'price' => $service->price,
                    'quantity' => $service->quantity,
                    'description' => $service->description,
                ]);

                $this->payInvoiceWithCredits($invoice->refresh());
            } catch (Exception $e) {
                DB::rollBack();
                $this->error('Error creating invoice for service ' . $service->id . ': ' . $e->getMessage());

                return;
            }

            DB::commit();

            $sendedInvoices++;
        });
        $this->info('Sending invoices if due date is ' . config('settings.cronjob_invoice', 7) . ' days away: ' . $sendedInvoices . ' invoices');

        // Cancel services if first invoice is not paid after x days
        $ordersCancelled = 0;
        Service::where('status', 'pending')->whereDoesntHave('invoices', function ($query) {
            $query->where('status', 'paid');
        })->where('created_at', '<', now()->subDays((int) config('settings.cronjob_order_cancel', 7)))->get()->each(function ($service) use (&$ordersCancelled) {
            $service->invoices()->where('status', 'pending')->update(['status' => 'cancelled']);

            $service->update(['status' => 'cancelled']);

            if ($service->product->stock) {
                $service->product->increment('stock', $service->quantity);
            }

            $ordersCancelled++;
        });
        $this->info('Cancelling services if first invoice is not paid after ' . config('settings.cronjob_order_cancel', 7) . ' days: ' . $ordersCancelled . ' orders');

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
        Service::where('status', 'active')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_order_suspend', 2)))->each(function ($service) use (&$ordersSuspended) {
            SuspendJob::dispatch($service);

            $service->update(['status' => 'suspended']);
            $ordersSuspended++;
        });
        $this->info('Suspending orders if due date is overdue for ' . config('settings.cronjob_order_suspend', 2) . ' days: ' . $ordersSuspended . ' orders');

        // Terminate orders if due date is overdue for x days
        $ordersTerminated = 0;
        Service::where('status', 'suspended')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_order_terminate', 14)))->each(function ($service) use (&$ordersTerminated) {
            TerminateJob::dispatch($service);
            $service->update(['status' => 'cancelled']);
            // Cancel outstanding invoices
            $service->invoices()->where('status', 'pending')->update(['status' => 'cancelled']);

            if ($service->product->stock) {
                $service->product->increment('stock', $service->quantity);
            }

            $ordersTerminated++;
        });
        $this->info('Terminating orders if due date is overdue for ' . config('settings.cronjob_order_terminate', 14) . ' days: ' . $ordersTerminated . ' orders');

        // Close tickets if no response for x days
        $ticketClosed = 0;
        Ticket::where('status', 'replied')->each(function ($ticket) use (&$ticketClosed) {
            $lastMessage = $ticket->messages()->latest('created_at')->first();
            if ($lastMessage && $lastMessage->created_at < now()->subDays((int) config('settings.cronjob_close_ticket', 7))) {
                $ticket->update(['status' => 'closed']);
                $ticketClosed++;
            }
        });
        $this->info('Closing tickets if no response for ' . config('settings.cronjob_close_ticket', 7) . ' days: ' . $ticketClosed . ' tickets');

        // Delete email logs older then x
        $this->info('Deleting email logs older then ' . config('settings.cronjob_delete_email_logs', 90) . ' days: ' . EmailLog::where('created_at', '<', now()->subDays((int) config('settings.cronjob_delete_email_logs', 30)))->count());
        EmailLog::where('created_at', '<', now()->subDays((int) config('settings.cronjob_delete_email_logs', 90)))->delete();

        // Check for updates
        $this->info('Checking for updates...');

        $this->call(CheckForUpdates::class);
    }

    private function payInvoiceWithCredits(Invoice $invoice): void
    {
        if (!config('settings.credits_auto_use', true)) {
            return;
        }
        $user = $invoice->user;
        $credits = $user->credits()->where('currency_code', $invoice->currency_code)->first();
        if ($invoice->remaining > 0 && $credits && $credits->amount >= $invoice->remaining) {
            $credits->amount -= $invoice->remaining;
            $credits->save();

            ExtensionHelper::addPayment($invoice->id, null, amount: $invoice->remaining);
        }
    }
}
