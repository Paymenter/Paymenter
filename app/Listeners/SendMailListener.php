<?php

namespace App\Listeners;

use App\Events\Auth\Login;
use App\Events\Invoice\Created as InvoiceCreated;
use App\Events\Order\Created as OrderCreated;
use App\Events\User\Created as UserCreated;
use App\Helpers\NotificationHelper;
use App\Models\Session;

class SendMailListener
{
    /**
     * Handle the event.
     */
    public function handle(InvoiceCreated|OrderCreated|UserCreated|Login $event): void
    {

        if ($event instanceof InvoiceCreated) {
            $invoice = $event->invoice;
            NotificationHelper::invoiceCreatedNotification($invoice->user, $invoice);
        } elseif ($event instanceof UserCreated) {
            $user = $event->user;
            NotificationHelper::emailVerificationNotification($user);
        } elseif ($event instanceof OrderCreated) {
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
        }
    }
}
