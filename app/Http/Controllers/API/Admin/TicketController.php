<?php

namespace App\Http\Controllers\API\Admin;

use App\Classes\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;

class TicketController extends Controller
{
    /**
     * Get all tickets.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTickets(Request $request)
    {
        $tickets = Ticket::paginate(25);

        return $this->success('Tickets successfully retrieved.', API::repaginate($tickets));
    }

    /**
     * Get a ticket by ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicket(Request $request, int $ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->firstOrFail();

        return $this->success('Ticket successfully retrieved.', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Get ticket messages by ticket ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicketMessages(Request $request, int $ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->firstOrFail();

        $messages = $ticket->messages()->paginate(25);

        return $this->success('Ticket messages successfully retrieved.', API::repaginate($messages));
    }

    /**
     * Create a new ticket.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTicket(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'message' => 'required',
            'priority' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        $ticket = Ticket::create([
            'title' => $request->title,
            'priority' => $request->priority,
            'user_id' => $request->user_id,
            'status' => 'open',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $request->message,
            'user_id' => $request->user()->id,
        ]);

        return $this->success('Ticket successfully created.', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Update ticket status.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTicketStatus(Request $request, int $ticketId)
    {
        $request->validate([
            'status' => 'required|in:open,closed',
        ]);

        $ticket = Ticket::where('id', $ticketId)->firstOrFail();

        $ticket->status = $request->status;
        $ticket->save();

        return $this->success('Ticket status successfully updated.', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Reply to a ticket.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function replyTicket(Request $request, int $ticketId)
    {
        $user = $request->user();

        $ticket = Ticket::where('id', $ticketId)->firstOrFail();

        $request->validate([
            'message' => 'required',
        ]);

        $message = json_decode($request->getContent())->message;

        if ($ticket->status == 'closed') {
            return $this->error('You cannot reply to a closed ticket.', 403);
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $message,
            'user_id' => $user->id,
        ]);

        return $this->success('Ticket successfully replied to.', [
            'ticket' => $ticket,
        ]);
    }
}
