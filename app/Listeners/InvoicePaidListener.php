<?php

namespace App\Listeners;

use App\Events\Invoice\Paid;
use App\Jobs\Server\CreateJob;
use App\Jobs\Server\UnsuspendJob;
use App\Models\Service;
use App\Models\ServiceUpgrade;

class InvoicePaidListener
{
    /**
     * Handle the event.
     */
    public function handle(Paid $event): void
    {
        // Update services if invoice is paid (suspended -> active etc.)
        $event->invoice->items->each(function ($item) {
            if ($item->reference_type == Service::class) {
                $service = $item->reference;
                if (!$service || $service->status == Service::STATUS_ACTIVE) {
                    return;
                }
                if ($service->product->server) {
                    if ($service->status == Service::STATUS_SUSPENDED) {
                        UnsuspendJob::dispatch($service);
                    } elseif ($service->status == Service::STATUS_PENDING) {
                        CreateJob::dispatch($service);
                    }
                }
                $service->status = Service::STATUS_ACTIVE;
                $service->expires_at = $service->calculateNextDueDate();
                $service->save();
            } else if ($item->reference_type == ServiceUpgrade::class) {
                $serviceUpgrade = $item->reference;
                if (!$serviceUpgrade || $serviceUpgrade->status !== ServiceUpgrade::STATUS_PENDING) {
                    return;
                }
                $serviceUpgrade->status = ServiceUpgrade::STATUS_COMPLETED;
                $serviceUpgrade->save();

                $service = $serviceUpgrade->service;
                $service->plan_id = $serviceUpgrade->plan_id;
                $service->price = $serviceUpgrade->price;
                $service->save();
            }
        });
    }
}
