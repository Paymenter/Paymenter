<?php

namespace App\Observers;

use App\Events\ServiceCancellation as ServiceCancellationEvent;
use App\Models\ServiceCancellation;

class ServiceCancellationObserver
{
    /**
     * Handle the ServiceCancellation "created" event.
     */
    public function created(ServiceCancellation $serviceCancellation): void
    {
        event(new ServiceCancellationEvent\Created($serviceCancellation));
    }

    /**
     * Handle the ServiceCancellation "updated" event.
     */
    public function updated(ServiceCancellation $serviceCancellation): void
    {
        event(new ServiceCancellationEvent\Updated($serviceCancellation));
    }

    /**
     * Handle the ServiceCancellation "deleted" event.
     */
    public function deleted(ServiceCancellation $serviceCancellation): void
    {
        event(new ServiceCancellationEvent\Deleted($serviceCancellation));
    }
}
