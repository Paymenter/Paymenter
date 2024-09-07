<?php

namespace App\Extensions\Gateways\Stripe\Listeners;

use App\Events\Service\Updated;
use App\Extensions\Gateways\Stripe\Stripe;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceListener implements ShouldQueue
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
        if (!$event->service->isDirty('price') && $event->service->properties->where('key', 'has_paypal_subscription')->first()?->value !== '1') {
            return;
        }
        $stripe = new Stripe;
        $stripe->updateSubscription($event->service);
    }
}
