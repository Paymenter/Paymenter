<?php

namespace App\Listeners;

use App\Events\TicketMessage\Created;
use App\Helpers\NotificationHelper;

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
            NotificationHelper::ticketMessageNotification($event->ticketMessage->ticket->user, $event->ticketMessage);
        } else {
            // Update ticket status
            $event->ticketMessage->ticket->update(['status' => 'open']);
        }
    }
}
