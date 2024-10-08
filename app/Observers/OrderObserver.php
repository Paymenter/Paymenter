<?php

namespace App\Observers;

use App\Events\Order as OrderEvent;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        event(new OrderEvent\Created($order));
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
