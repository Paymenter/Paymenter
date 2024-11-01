<?php

namespace App\Listeners;

use App\Events\Invoice\Created as InvoiceCreated;
use App\Events\Order\Created as OrderCreated;
use App\Events\User\Created as UserCreated;
use App\Helpers\NotificationHelper;

class SendMailListener
{
    /**
     * Handle the event.
     */
    public function handle(InvoiceCreated|OrderCreated|UserCreated $event): void
    {

        if ($event instanceof InvoiceCreated) {
            $invoice = $event->invoice;
            NotificationHelper::invoiceCreatedNotification($invoice->user, $invoice);
        } elseif ($event instanceof UserCreated) {
            $user = $event->user;
            NotificationHelper::emailVerificationNotification($user);
        } else {
            $order = $event->order;
            NotificationHelper::orderCreatedNotification($order->user, $order);
        }
    }
}
