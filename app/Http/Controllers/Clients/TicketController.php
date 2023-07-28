<?php

namespace App\Http\Controllers\Clients;

use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tickets = $user->tickets()->get();
        $ticketMessages = TicketMessage::all();
        $sort = $request->get('sort');

        return view(
            'clients.tickets.index',
            compact(
                'tickets',
                'user',
                'ticketMessages',
                'sort'
            )
        );
    }

    public function create()
    {
        $services = Auth::user()->orders;

        return view('clients.tickets.create', compact('services'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $body = $request->validate([
            'g-recaptcha-response' => 'recaptcha',
            'cf-turnstile-response' => 'recaptcha',
            'title' => 'required',
            'description' => 'required',
            'priority' => 'required',
            'h-captcha-response' => 'recaptcha',
        ]);

        if (RateLimiter::tooManyAttempts("create-ticket:$user->id", $perMinute = 1)) {
            return redirect()->back()->with('error', 'You are sending too many messages. Please wait a few minutes and try again.');
        }
        RateLimiter::hit("create-ticket:$user->id");

        $ticket = new Ticket();
        $ticket->title = $body['title'];
        $ticket->status = 'open';
        $ticket->user()->associate($user);
        $ticket->priority = $body['priority'];
        $ticket->save();

        $ticketMessage = new TicketMessage();
        $ticketMessage->ticket_id = $ticket->id;
        $ticketMessage->message = $body['description'];
        $ticketMessage->user()->associate($user);
        $ticketMessage->save();

        NotificationHelper::sendNewTicketNotification($ticket, $user);

        return redirect()->route('clients.tickets.show', $ticket)->with('success', 'Ticket has been created');
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id != Auth::user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to view this ticket.');
        }
        $messages = TicketMessage::where('ticket_id', $ticket->id)->get();

        return view('clients.tickets.show', compact('ticket', 'messages'));
    }

    public function close(Ticket $ticket)
    {
        if ($ticket->status == 'closed') {
            return redirect()->back()->with('error', 'Ticket already closed.');
        }

        $ticket->status = 'closed';
        $ticket->save();

        return redirect('/tickets')->with('success', 'Ticket closed successfully');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        $body = $request->validate([
            'g-recaptcha-response' => 'recaptcha',
            'cf-turnstile-response' => 'recaptcha',
            'h-captcha-response' => 'recaptcha',
            'message' => 'required',
        ]);

        if (RateLimiter::tooManyAttempts("send-message:$user->id", $perMinute = 5)) {
            return redirect()->back()->with('error', 'You are sending too many messages. Please wait a few minutes and try again.');
        }
        RateLimiter::hit("send-message:$user->id");

        $ticketMessage = new TicketMessage();
        $ticketMessage->ticket_id = $ticket->id;
        $ticketMessage->message = $body['message'];
        $ticketMessage->user()->associate($user);
        $ticketMessage->save();

        $ticket->status = 'open';
        $ticket->save();

        NotificationHelper::sendNewTicketMessageNotification($ticket, $user);
        if ($ticket->assigned_to) {
            NotificationHelper::sendNewTicketMessageNotification($ticket, User::where('id', $ticket->assigned_to)->first());
        }

        return redirect()->back()->with('success', 'Message sent successfully');
    }
}
