<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Jobs\Server\SuspendJob;
use App\Jobs\Server\TerminateJob;
use App\Models\EmailLog;
use App\Models\OrderProduct;
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
        OrderProduct::where('status', 'active')->where('expires_at', '<', now()->addDays((int) config('settings.cronjob_invoice')))->get()->each(function ($orderProduct) use (&$sendedInvoices) {
            // Does the order product have already a pending invoice?
            if ($orderProduct->invoices()->where('status', 'pending')->exists()) {
                return;
            }

            // Create invoice
            $invoice = $orderProduct->invoices()->create([
                'user_id' => $orderProduct->order->user_id,
                'status' => 'pending',
                'issued_at' => now(),
                'due_at' => $orderProduct->expires_at,
                'currency_code' => $orderProduct->order->currency_code,
            ]);

            // Create invoice items
            $invoice->items()->create([
                'order_product_id' => $orderProduct->id,
                'price' => $orderProduct->price,
                'quantity' => $orderProduct->quantity,
                'description' => $orderProduct->description,
            ]);

            // Send email
            NotificationHelper::newInvoiceCreatedNotification($orderProduct->order->user, $invoice);

            $sendedInvoices++;
        });
        $this->info('Sending invoices if due date is ' . config('settings.cronjob_invoice') . ' days away: ' . $sendedInvoices . ' invoices');

        // Suspend orders if due date is overdue for x days
        $ordersSuspended = 0;
        OrderProduct::where('status', 'active')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_suspend')))->each(function ($orderProduct) use (&$ordersSuspended) {
            SuspendJob::dispatch($orderProduct);

            $orderProduct->update(['status' => 'suspended']);
            $ordersSuspended++;
        });
        $this->info('Suspending orders if due date is overdue for ' . config('settings.cronjob_suspend') . ' days: ' . $ordersSuspended . ' orders');

        // Terminate orders if due date is overdue for x days
        $ordersTerminated = 0;
        OrderProduct::where('status', 'suspended')->where('expires_at', '<', now()->subDays((int) config('settings.cronjob_terminate')))->each(function ($orderProduct) use (&$ordersTerminated) {
            TerminateJob::dispatch($orderProduct);
            $orderProduct->update(['status' => 'cancelled']);
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
