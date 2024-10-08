<?php

namespace App\Observers;

use App\Events\TicketMessage as TicketEvent;
use App\Models\TicketMessage;

class TicketMessageObserver
{
    /**
     * Handle the TicketMessage "created" event.
     */
    public function created(TicketMessage $ticketMessage): void
    {
        event(new TicketEvent\Created($ticketMessage));
    }

    /**
     * Handle the TicketMessage "uçpdated" event.
     */
    public function updated(TicketMessage $ticketMessage): void
    {
        event(new TicketEvent\Updated($ticketMessage));
    }

    /**
     * Handle the TicketMessage "deleted" event.
     */
    public function deleted(TicketMessage $ticketMessage): void
    {
        event(new TicketEvent\Deleted($ticketMessage));
    }
}
