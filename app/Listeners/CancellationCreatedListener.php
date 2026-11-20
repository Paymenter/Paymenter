<?php

namespace App\Listeners;

use App\Events\ServiceCancellation\Created;
use App\Jobs\Server\TerminateJob;
use App\Models\Service;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancellationCreatedListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        if ($event->cancellation->type == 'immediate') {
            if (in_array($event->cancellation->service->status, [Service::STATUS_ACTIVE, Service::STATUS_SUSPENDED])) {
                TerminateJob::dispatch($event->cancellation->service);
            }

            $event->cancellation->service->update([
                'status' => 'cancelled',
            ]);

            $service = $event->cancellation->service;
            $service->invoices()->where('status', 'pending')->get()->each(function ($invoice) use ($service) {
                $items = $invoice->items()->where('reference_type', Service::class)->where('reference_id', $service->id)->get();
                // Keep invoices with other items; only remove this service's lines
                if ($invoice->items()->count() > $items->count()) {
                    $items->each->delete();
                    if ($invoice->refresh()->remaining <= 0) {
                        $invoice->update(['status' => 'paid']);
                    }
                } else {
                    $invoice->newQuery()->whereKey($invoice->id)->update(['status' => 'cancelled']);
                }
            });

            if ($event->cancellation->service->product->stock) {
                $event->cancellation->service->product->increment('stock', $event->cancellation->service->quantity);
            }
        }
        // If the cancellation is scheduled, we don't need to do anything as it will be handled by the cron job
    }
}
