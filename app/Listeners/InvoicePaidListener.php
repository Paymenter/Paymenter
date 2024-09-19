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
            if ($item->reference_type !== 'App\Models\Service') {
                return;
            }
            $service = $item->reference;
            if (!$service || $service->status == 'active') {
                return;
            }
            if ($service->product->server) {
                if ($service->status == 'suspended') {
                    UnsuspendJob::dispatch($service);
                } elseif ($service->status == 'pending') {
                    CreateJob::dispatch($service);
                }
            }
            $service->status = 'active';
            $service->expires_at = $service->calculateNextDueDate();
            $service->save();
        });
    }
}
