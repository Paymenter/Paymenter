<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Jobs\Server\SuspendJob;
use App\Jobs\Server\TerminateJob;
use App\Models\EmailLog;
use App\Models\InvoiceItem;
use App\Models\Service;
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
    protected $description = 'Command description';

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
                    $price = $service->plan->prices()->where('currency_code', $service->order->currency_code)->first()->price;
                    $service->price = $price;
                    $service->save();
                }
            }

            // Create invoice
            $invoice = $service->invoices()->create([
                'user_id' => $service->order->user_id,
                'status' => 'pending',
                'issued_at' => now(),
                'due_at' => $service->expires_at,
                'currency_code' => $service->order->currency_code,
            ]);

            // Create invoice items
            $invoice->items()->create([
                'reference_id' => $service->id,
                'reference_type' => InvoiceItem::class,
                'price' => $service->price,
                'quantity' => $service->quantity,
                'description' => $service->description,
            ]);

            // Send email
            NotificationHelper::newInvoiceCreatedNotification($service->order->user, $invoice);

            $sendedInvoices++;
        });
        $this->info('Sending invoices if due date is ' . config('settings.cronjob_invoice') . ' days away: ' . $sendedInvoices . ' invoices');

        // Suspend orders if due date is overdue for x days
        $ordersSuspended = 0;
        Service::where('status', 'active')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_suspend')))->each(function ($service) use (&$ordersSuspended) {
            SuspendJob::dispatch($service);

            $service->update(['status' => 'suspended']);
            $ordersSuspended++;
        });
        $this->info('Suspending orders if due date is overdue for ' . config('settings.cronjob_suspend') . ' days: ' . $ordersSuspended . ' orders');

        // Terminate orders if due date is overdue for x days
        $ordersTerminated = 0;
        Service::where('status', 'suspended')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_terminate')))->each(function ($service) use (&$ordersTerminated) {
            TerminateJob::dispatch($service);
            $service->update(['status' => 'cancelled']);
            $ordersTerminated++;
        });
        $this->info('Terminating orders if due date is overdue for ' . config('settings.cronjob_terminate') . ' days: ' . $ordersTerminated . ' orders');

        // Close tickets if no response for x days
        $ticketClosed = 0;
        Ticket::where('status', 'open')->each(function ($ticket) use (&$ticketClosed) {
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
