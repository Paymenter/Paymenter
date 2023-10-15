<?php

namespace App\Events\Ticket;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TicketMessage $message)
    {
        $this->message = $message;
        $this->ticket = $message->ticket;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
