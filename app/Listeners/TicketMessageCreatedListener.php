<?php

namespace App\Listeners;

use App\Events\TicketMessage\Created;
use App\Helpers\NotificationHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TicketMessageCreatedListener
{
    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        if ($event->ticketMessage->ticket->user->id !== $event->ticketMessage->user->id) {
            // Update ticket status
            $event->ticketMessage->ticket->update(['status' => 'replied']);
            // Send notification to ticket owner
            NotificationHelper::newTicketMessageNotification($event->ticketMessage->ticket->user, $event->ticketMessage);
        } else {
            // Update ticket status
            $event->ticketMessage->ticket->update(['status' => 'open']);
        }
    }
}
