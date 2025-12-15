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

            $event->cancellation->service->invoices()->where('status', 'pending')->update(['status' => 'cancelled']);

            if ($event->cancellation->service->product->stock) {
                $event->cancellation->service->product->increment('stock', $event->cancellation->service->quantity);
            }
        }
        // If the cancellation is scheduled, we don't need to do anything as it will be handled by the cron job
    }
}
