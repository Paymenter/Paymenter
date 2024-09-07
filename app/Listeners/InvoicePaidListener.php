<?php

namespace App\Listeners;

use App\Events\Invoice\Paid;
use App\Jobs\Server\CreateJob;
use App\Jobs\Server\UnsuspendJob;

class InvoicePaidListener
{
    /**
     * Handle the event.
     */
    public function handle(Paid $event): void
    {
        // Update services if invoice is paid (suspended -> active etc.)
        $event->invoice->items->each(function ($item) {
            $service = $item->service;
            if (!$service || $service->status == 'active' || !$service->product->server) {
                return;
            }
            if ($service->status == 'suspended') {
                UnsuspendJob::dispatch($service);
            } elseif ($service->status == 'pending') {
                CreateJob::dispatch($service);
                $service->status = 'pending-setup';
            }
            $service->status = 'active';
            $service->expires_at = $service->calculateNextDueDate();
            $service->save();
        });
    }
}
