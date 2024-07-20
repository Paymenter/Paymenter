<?php

namespace App\Extensions\Gateways\PayPal\Listeners;

use App\Events\OrderProduct\Updated;
use App\Extensions\Gateways\PayPal\PayPal;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderProductListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Updated $event): void
    {
        // If price isn't changed, do nothing
        if (!$event->orderProduct->isDirty('price') && $event->orderProduct->properties->where('key', 'has_paypal_subscription')->first()?->value !== '1') {
            return;
        }
        $paypal = new PayPal();
        $paypal->updateSubscription($event->orderProduct);
    }
}
