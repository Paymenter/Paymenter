<?php
namespace App\Http\Controllers\API\Clients;

use App\Classes\API;
use App\Http\Controllers\Controller;
use App\Models\TicketMessages;
use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class TicketController extends Controller
{
    /**
     * Get all tickets of current user
     */
    public function getTickets(Request $request) {
        $user = $request->user();
        $tickets = $user->tickets()->paginate(25);

        return response()->json([
            'tickets' => API::repaginate($tickets)
        ], 200);
    }

    /**
     * Create a new ticket
     */
    public function createTicket(Request $request, int $ticketId) {
        $user = $request->user();

        $request->validate([
            'title' => 'required',
            'message' => 'required',
            'priority' => 'required',
        ]);
        
        $body = json_decode($request->getContent());

        $executed = RateLimiter::attempt(
            'create-ticket:' . $user->id,
            $perMinute = 1,
            function () {
                return true;
            }
        );

        if (!$executed) {
            return response()->json([
                'error' => 'You are creating too many new tickets. Please wait a few minutes and try again.'
            ], 429);
        }

        $ticket = new Tickets();
        $ticket->title = $body->title;
        $ticket->status = 'open';
        $ticket->client = $user->id;
        $ticket->priority = $body->priority;
        $ticket->save();

        TicketMessages::create([
            'ticket_id' => $ticket->id,
            'message' => $body->message,
            'user_id' => $user->id,
        ]);
        
        return response()->json([
            'message' => 'Ticket was successfully created.',
            'ticket' => $ticket
        ], 204);
    }

    /**
     * Get a ticket by ID
     */
    public function getTicket(Request $request, int $ticketId) {
        $user = $request->user();
        $ticket = Tickets::where('client', $user->id)->where('id', $ticketId)->firstOrFail();

        return response()->json([
            'ticket' => $ticket
        ], 200);
    }

    /**
     * Close a ticket by ID
     */
    public function closeTicket(Request $request, int $ticketId) {
        $user = $request->user();
        $ticket = Tickets::where('client', $user->id)->where('id', $ticketId)->firstOrFail();

        $ticket->status = 'closed';
        $ticket->save();

        return response()->json([
            'message' => "Ticket #{$ticket->id} was successfully closed.}"
        ], 200);
    }

    /**
     * Reply to a ticket
     */
    public function replyTicket(Request $request, int $ticketId) {
        $user = $request->user();
        $ticket = Tickets::where('client', $user->id)->where('id', $ticketId)->firstOrFail();

        $request->validate([
            'message' => 'required',
        ]);
        
        $message = json_decode($request->getContent())->message;

        if($ticket->status == 'closed'){
            return response()->json([
                'error' => 'This ticket is closed and thus cannot be replied to.'
            ], 403);
        }

        $executed = RateLimiter::attempt(
            'send-message:' . $ticket->id,
            $perMinute = 3,
            function () {
                return true;
            }
        );

        if (!$executed) {
            return response()->json([
                'error' => 'You are sending too many messages. Please wait a few minutes and try again.'
            ], 429);
        }

        TicketMessages::create([
            'ticket_id' => $ticket->id,
            'message' => $message,
            'user_id' => $user->id,
        ]);
        
        return response()->json([
            'message' => 'Message was successfully added to ticket.'
        ], 204);
    }
}