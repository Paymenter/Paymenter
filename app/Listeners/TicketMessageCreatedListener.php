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

        if (!is_null($ticketUserId)) {
            if ($messageUserId === $ticketUserId) {
                $ticket->update(['status' => 'open']);

                return;
            }

            if (is_null($messageUserId)) {
                // Defensive fallback: treat unknown sender on user-owned tickets as client activity.
                $ticket->update(['status' => 'open']);

                return;
            }

            $ticket->update(['status' => 'replied']);

            if ($ticket->user) {
                NotificationHelper::ticketMessageNotification($ticket->user, $event->ticketMessage);
            }

            return;
        }

        if (!is_null($messageUserId)) {
            $ticket->update(['status' => 'replied']);

            if ($ticket->guest_email) {
                NotificationHelper::guestTicketMessageNotification($ticket->guest_email, $event->ticketMessage);
            }

            return;
        }

        $ticket->update(['status' => 'open']);
    }
}
