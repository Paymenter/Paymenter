<?php

namespace App\Observers;

use App\Events\Ticket as TicketEvent;
use App\Models\Ticket;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        event(new TicketEvent\Created($ticket));
    }

    /**
     * Handle the Ticket "uçpdated" event.
     */
    public function updated(Ticket $ticket): void
    {
        event(new TicketEvent\Updated($ticket));
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        event(new TicketEvent\Deleted($ticket));
    }
}
