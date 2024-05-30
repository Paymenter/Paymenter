<?php

namespace App\Observers;

use App\Events\Ticket\TicketCreated;
use App\Events\Ticket\TicketUpdated;
use App\Models\Ticket;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     *
     * @return void
     */
    public function created(Ticket $ticket)
    {
        event(new TicketCreated($ticket));
    }

    /**
     * Handle the Ticket "updated" event.
     *
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        event(new TicketUpdated($ticket));
    }

    /**
     * Handle the Ticket "deleted" event.
     *
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     *
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
        //
    }
}
