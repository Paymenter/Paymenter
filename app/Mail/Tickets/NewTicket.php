<?php

namespace App\Mail\Tickets;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use App\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class NewTicket extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
