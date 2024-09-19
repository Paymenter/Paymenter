<?php

namespace App\Listeners;

use App\Events\ServiceCancellation\Created;
use App\Jobs\Server\TerminateJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancellationCreatedListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        if ($event->cancellation->type == 'immediate') {
            TerminateJob::dispatch($event->cancellation->service);

            $event->cancellation->service->update([
                'status' => 'cancelled',
            ]);
        }
        // If the cancellation is scheduled, we don't need to do anything as it will be handled by the cron job
    }
}
