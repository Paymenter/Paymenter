<?php

namespace App\Services\Service;

use App\Jobs\Server\CreateJob;
use App\Jobs\Server\UnsuspendJob;
use App\Models\Service;

class RenewServiceService
{
    /**
     * Handle the service renewal.
     *
     * @return void
     */
    public function handle(Service $service)
    {
        if ($service->product->server) {
            if ($service->status == Service::STATUS_SUSPENDED) {
                UnsuspendJob::dispatch($service);
            } elseif (in_array($service->status, [Service::STATUS_PENDING, Service::STATUS_CANCELLED])) {
                // A cancelled service has no server: it was never created or it was terminated
                CreateJob::dispatch($service);
            }
        }

        $service->expires_at = $service->calculateNextDueDate();
        $service->status = Service::STATUS_ACTIVE;
        $service->save();
    }
}
