<?php

namespace App\Listeners;

use App\Events\Auth\Login;
use App\Events\Invoice\Finalized as InvoiceFinalized;
use App\Events\Order\Finalized as OrderFinalized;
use App\Events\ServiceCancellation\Created as CancellationCreated;
use App\Events\User\Created as UserCreated;
use App\Helpers\NotificationHelper;
use App\Models\Session;

class SendMailListener
{
    /**
     * Handle the event.
     */
    public function handle(InvoiceFinalized|OrderFinalized|UserCreated|Login|CancellationCreated $event): void
    {

        if ($event instanceof InvoiceFinalized) {
            if ($event->sendEmail === false) {
                return;
            }

            $invoice = $event->invoice;
            NotificationHelper::invoiceCreatedNotification($invoice->user, $invoice);
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
            $user = $event->user;
            $data = [
                'ip' => request()->ip(),
                'device' => (new Session(['user_agent' => request()->userAgent()]))->getFormattedDeviceAttribute(),
                'time' => now()->format('Y-m-d H:i:s'),
            ];
            NotificationHelper::loginDetectedNotification($user, $data);
        } elseif ($event instanceof CancellationCreated) {
            $cancellation = $event->cancellation;
            NotificationHelper::serviceCancellationReceivedNotification($cancellation->service->user, $cancellation);
        }
    }
}
