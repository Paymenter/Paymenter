<?php

namespace App\Http\Controllers\API\Admin;

use App\Classes\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketController extends Controller
{
    /**
     * Get all tickets.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTickets(Request $request)
    {
        $user = $request->user();

        if (!$user->tokenCan('admin:ticket:read')) {
            return response()->json([
                'error' => 'You do not have permission to read tickets.',
            ], 403);
        }

        $tickets = Ticket::paginate(25);

        return response()->json([
            'tickets' => API::repaginate($tickets),
        ], 200);
    }

    /**
     * Get a ticket by ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicket(Request $request, int $ticketId)
    {
        $user = $request->user();

        if (!$user->tokenCan('admin:ticket:read')) {
            return response()->json([
                'error' => 'You do not have permission to read tickets.',
            ], 403);
        }

        $ticket = Ticket::where('id', $ticketId)->firstOrFail();

        return response()->json([
            'ticket' => $ticket,
        ], 200);
    }

    /**
     * Create a new ticket.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTicket(Request $request)
    {
        $user = $request->user();

        if (!$user->tokenCan('admin:ticket:create')) {
            return response()->json([
                'error' => 'You do not have permission to create tickets.',
            ], 403);
        }

        $request->validate([
            'title' => 'required',
            'message' => 'required',
            'priority' => 'required',
            'user_id' => 'required',
        ]);

        $body = json_decode($request->getContent());

        $ticket = Ticket::create([
            'title' => $body->title,
            'priority' => $body->priority,
            'user_id' => $body->user_id,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $body->message,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => "Ticket #{$ticket->id} was successfully created.",
        ], 200);
    }
    /**
     * Close a ticket by ID.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function closeTicket(Request $request, int $ticketId)
    {
        $user = $request->user();
        
        if (!$user->tokenCan('admin:ticket:update')) {
            return response()->json([
                'error' => 'You do not have permission to update tickets.',
            ], 403);
        }

        $ticket = Ticket::where('id', $ticketId)->firstOrFail();

        $ticket->status = 'closed';
        $ticket->save();

        return response()->json([
            'message' => "Ticket #{$ticket->id} was successfully closed.",
        ], 200);
    }

    /**
     * Reply to a ticket.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function replyTicket(Request $request, int $ticketId)
    {
        $user = $request->user();
        
        if (!$user->tokenCan('admin:ticket:update')) {
            return response()->json([
                'error' => 'You do not have permission to update tickets.',
            ], 403);
        }

        $ticket = Ticket::where('id', $ticketId)->firstOrFail();

        $request->validate([
            'message' => 'required',
        ]);

        $message = json_decode($request->getContent())->message;

        if ($ticket->status == 'closed') {
            return response()->json([
                'error' => 'This ticket is closed and thus cannot be replied to.',
            ], 403);
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $message,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Message was successfully added to ticket.',
        ], 204);
    }
}
