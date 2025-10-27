<?php

namespace App\Listeners;

use App\Enums\InvoiceTransactionStatus;
use App\Events\Auth\Login;
use App\Events\Invoice\Finalized as InvoiceFinalized;
use App\Events\Invoice\Paid as InvoicePaid;
use App\Events\InvoiceTransaction\Created as InvoiceTransactionCreated;
use App\Events\InvoiceTransaction\Updated as InvoiceTransactionUpdated;
use App\Events\Order\Finalized as OrderFinalized;
use App\Events\ServiceCancellation\Created as CancellationCreated;
use App\Events\User\Created as UserCreated;
use App\Helpers\NotificationHelper;
use App\Models\Session;
use App\Models\UserAuthenticationLog;

class SendMailListener
{
    /**
     * Handle the event.
     */
    public function handle(InvoiceFinalized|OrderFinalized|UserCreated|Login|CancellationCreated|InvoicePaid|InvoiceTransactionCreated|InvoiceTransactionUpdated $event): void
    {

        if ($event instanceof InvoiceFinalized) {
            if ($event->sendEmail === false) {
                return;
            }

            $invoice = $event->invoice;
            NotificationHelper::invoiceCreatedNotification($invoice->user, $invoice);
        } elseif ($event instanceof InvoicePaid) {
            NotificationHelper::invoicePaidNotification($event->invoice->user, $event->invoice);
        } elseif ($event instanceof InvoiceTransactionCreated || $event instanceof InvoiceTransactionUpdated) {
            // Check if status is failed
            $transaction = $event->invoiceTransaction;
            if ($transaction->status === InvoiceTransactionStatus::Failed) {
                NotificationHelper::invoicePaymentFailedNotification($transaction->invoice->user, $transaction->invoice);
            }
        } elseif ($event instanceof UserCreated) {
            $user = $event->user;
            NotificationHelper::emailVerificationNotification($user);
        } elseif ($event instanceof OrderFinalized) {
            if ($event->sendEmail === false) {
                return;
            }
            $order = $event->order;
            NotificationHelper::orderCreatedNotification($order->user, $order);
        } elseif ($event instanceof Login) {
            $this->login($event);
        } elseif ($event instanceof CancellationCreated) {
            $cancellation = $event->cancellation;
            NotificationHelper::serviceCancellationReceivedNotification($cancellation->service->user, $cancellation);
        }
    }

    private function login(Login $event): void
    {
        $user = $event->user;
        $ip = request()->ip();
        if (!$user->authenticationLogs()->where('ip_address', $ip)->exists()) {
            // If the log for this IP does not exist, create a new log
            $log = new UserAuthenticationLog;
            $log->user_id = $user->id;
            $log->ip_address = $ip;
            $log->save();

            $data = [
                'ip' => $ip,
                'device' => (new Session(['user_agent' => request()->userAgent()]))->getFormattedDeviceAttribute(),
                'time' => now()->format('Y-m-d H:i:s'),
            ];
            NotificationHelper::loginDetectedNotification($user, $data);
        } else {
            // If it exists, update the last used timestamp
            $log = $user->authenticationLogs()->where('ip_address', $ip)->first();
            $log->last_used_at = now();
            $log->save();
        }
    }
}
