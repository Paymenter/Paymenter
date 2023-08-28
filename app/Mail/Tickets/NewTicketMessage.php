<?php
namespace App\Mail\Tickets;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class NewTicketMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $ticket;
    public $message;

    /**
     * Create a new message instance.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->message = $ticket->messages()->latest()->first();
        $this->subject('New ticket message');
    }

    /**
     * Get the message content definition.
     * 
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.tickets.new-message',
            with: [
                'ticket' => $this->ticket,
            ]
        );
    }
}