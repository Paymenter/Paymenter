<?php

namespace App\Observers;

use App\Models\ServiceUpgrade;
use App\Events\ServiceUpgrade as ServiceUpgradeEvent;

class ServiceUpgradeObserver
{
    /**
     * Handle the ServiceUpgrade "created" event.
     */
    public function created(ServiceUpgrade $serviceUpgrade): void
    {
        event(new ServiceUpgradeEvent\Created($serviceUpgrade));
    }

    /**
     * Handle the ServiceUpgrade "updated" event.
     */
    public function updated(ServiceUpgrade $serviceUpgrade): void
    {
        event(new ServiceUpgradeEvent\Updated($serviceUpgrade));
    }

    /**
     * Handle the ServiceUpgrade "deleted" event.
     */
    public function deleted(ServiceUpgrade $serviceUpgrade): void
    {
        event(new ServiceUpgradeEvent\Deleted($serviceUpgrade));
    }
}
