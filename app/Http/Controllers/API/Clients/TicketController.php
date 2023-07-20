<?php

namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Http\Controllers\API\Controller;
use App\Http\Requests\API\TicketRequest;
use Illuminate\Support\Facades\RateLimiter;

class TicketController extends Controller
{
    /**
     * Get all tickets of current user.
     */
    public function getTickets(Request $request)
    {
        $user = $request->user();

        $tickets = $user->tickets()->paginate(config('app.pagination'));

        return $this->success('Tickets successfully retrieved.', API::repaginate($tickets));
    }

    /**
     * Create a new ticket.
     */
    public function createTicket(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'message' => 'required',
            'priority' => 'required|in:low,medium,high',
        ]);

        $user = $request->user();

        $executed = RateLimiter::attempt(
            'create-ticket:' . $user->id,
            $perMinute = 1,
            function () {
                return true;
            }
        );

        if (!$executed) {
            return $this->error('You are creating too many tickets. Please wait a few minutes and try again.', 429);
        }

        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->status = 'open';
        $ticket->user_id = $user->id;
        $ticket->priority = $request->priority;
        $ticket->save();

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $request->message,
            'user_id' => $user->id,
        ]);

        return $this->success('Ticket successfully created.', [
            'ticket' => $ticket,
        ], 201);
    }

    /**
     * Get a ticket by ID.
     */
    public function getTicket(Request $request, int $ticketId)
    {
        $user = $request->user();

        $ticket = Ticket::where('user_id', $user->id)->where('id', $ticketId)->firstOrFail();

        return $this->success('Ticket successfully retrieved.', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Get messages of a ticket by ID.
     */
    public function getMessages(Request $request, int $ticketId)
    {
        $user = $request->user();

        $ticket = Ticket::where('user_id', $user->id)->where('id', $ticketId)->firstOrFail();

        //Get ticket messages with user id and name 
        $messages = $ticket->messages()->with('user')->paginate(config('app.pagination'));

        return $this->success('Messages successfully retrieved.', API::repaginate($messages));
    }

    /**
     * Close a ticket by ID.
     */
    public function closeTicket(Request $request, int $ticketId)
    {
        $user = $request->user();

        $ticket = Ticket::where('user_id', $user->id)->where('id', $ticketId)->firstOrFail();

        $ticket->status = 'closed';
        $ticket->save();

        return $this->success('Ticket successfully closed.');
    }

    /**
     * Reply to a ticket.
     */
    public function replyTicket(Request $request, int $ticketId)
    {
        $user = $request->user();

        $request->validate([
            'message' => 'required',
        ]);

        $ticket = Ticket::where('user_id', $user->id)->where('id', $ticketId)->firstOrFail();

        if ($ticket->status == 'closed') {
            return $this->error('You cannot reply to a closed ticket.', 403);
        }

        $executed = RateLimiter::attempt(
            'send-message:' . $ticket->id,
            $perMinute = 3,
            function () {
                return true;
            }
        );

        if (!$executed) {
            return $this->error('You are sending too many messages. Please wait a few minutes and try again.', 429);
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $request->message,
            'user_id' => $user->id,
        ]);

        return $this->success('Message successfully sent.', [], 201);
    }
}
