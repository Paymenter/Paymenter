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
        $ticket = $event->ticketMessage->ticket;
        $ticketUserId = $ticket->user_id;
        $messageUserId = $event->ticketMessage->user_id;

        if ($ticketUserId) {
            if ($ticketUserId !== $messageUserId) {
                $ticket->update(['status' => 'replied']);

                if ($ticket->user) {
                    NotificationHelper::ticketMessageNotification($ticket->user, $event->ticketMessage);
                }

                return;
            }

            $ticket->update(['status' => 'open']);

            return;
        }

        if (is_null($messageUserId)) {
            $ticket->update(['status' => 'open']);

            return;
        }

        $ticket->update(['status' => 'replied']);

        if ($ticket->guest_email) {
            NotificationHelper::guestTicketMessageNotification($ticket->guest_email, $event->ticketMessage);
        }
    }
}
