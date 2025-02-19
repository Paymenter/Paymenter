<?php

namespace App\Console\Commands;

use App\Events\Invoice\Created as InvoiceCreated;
use App\Helpers\NotificationHelper;
use App\Jobs\Server\SuspendJob;
use App\Jobs\Server\TerminateJob;
use App\Models\EmailLog;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\Ticket;
use Illuminate\Console\Command;

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
            if ($service->invoices()->where('status', 'pending')->exists()) {
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
                'price' => $upgrade->calculatePrice(),
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
        Service::where('status', 'suspended')->where('expires_at', '<', now()->subDays((int) config('settings.cronjobb_order_terminate')))->each(function ($service) use (&$ordersTerminated) {
            TerminateJob::dispatch($service);
            $service->update(['status' => 'cancelled']);
            $ordersTerminated++;
        });
        $this->info('Terminating orders if due date is overdue for ' . config('settings.cronjobb_order_terminate') . ' days: ' . $ordersTerminated . ' orders');

        // Close tickets if no response for x days
        $ticketClosed = 0;
        Ticket::where('status', 'replied')->each(function ($ticket) use (&$ticketClosed) {
            if ($ticket->messages()->where('user_id', '!=', $ticket->user_id)->where('created_at', '<', now()->subDays((int) config('settings.cronjob_close_ticket')))->exists()) {
                $ticket->update(['status' => 'closed']);
                $ticketClosed++;
            }
        });
        $this->info('Closing tickets if no response for ' . config('settings.cronjob_close_ticket') . ' days: ' . $ticketClosed . ' tickets');

        // Delete email logs older then x
        $this->info('Deleting email logs older then ' . config('settings.cronjob_delete_email_logs') . ' days: ' . EmailLog::where('created_at', '<', now()->subDays((int) config('settings.cronjob_delete_email_logs')))->count());
        EmailLog::where('created_at', '<', now()->subDays((int) config('settings.cronjob_delete_email_logs')))->delete();
    }
}
