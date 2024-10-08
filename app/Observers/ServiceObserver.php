<?php

namespace App\Observers;

use App\Events\Service as ServiceEvent;
use App\Models\Service;

class ServiceObserver
{
    /**
     * Handle the Service "created" event.
     */
    public function created(Service $service): void
    {
        event(new ServiceEvent\Created($service));
    }

    /**
     * Handle the Service "updated" event.
     */
    public function updated(Service $service): void
    {
        event(new ServiceEvent\Updated($service));
    }

    /**
     * Handle the Service "deleted" event.
     */
    public function deleted(Service $service): void
    {
        event(new ServiceEvent\Deleted($service));
    }
}
