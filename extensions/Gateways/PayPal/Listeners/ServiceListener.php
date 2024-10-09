<?php

namespace Paymenter\Extensions\Gateways\PayPal\Listeners;

use App\Events\Service\Updated;
use Paymenter\Extensions\Gateways\PayPal\PayPal;
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
        $paypal = new PayPal;
        $paypal->updateSubscription($event->service);
    }
}
