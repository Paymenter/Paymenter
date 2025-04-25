<?php

namespace App\Observers;

use App\Events\Order as OrderEvent;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     */
    public function creating(Order $order): void
    {
        event(new OrderEvent\Creating($order));
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        event(new OrderEvent\Created($order));

        $sendEmail = $order->send_create_email;

        dispatch(function () use ($order, $sendEmail) {
            event(new OrderEvent\Finalized($order, $sendEmail));
        })->afterResponse();
    }

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        event(new OrderEvent\Updating($order));
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        event(new OrderEvent\Updated($order));
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        event(new OrderEvent\Deleted($order));
    }
}
