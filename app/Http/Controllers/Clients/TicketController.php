<?php

namespace App\Http\Controllers\Clients;

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::where('client', auth()->user()->id)->get();
        $users = User::where('id', auth()->user()->id)->get();
        $ticketMessages = TicketMessage::all();
        $sort = $request->get('sort');

        return view(
            'clients.tickets.index',
            compact(
                'tickets',
                'users',
                'ticketMessages',
                'sort'
            )
        );
    }

    public function create()
    {
        $services = Order::where('client', auth()->user()->id)->get();

        return view('clients.tickets.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'g-recaptcha-response' => 'recaptcha',
            'cf-turnstile-response' => 'recaptcha',
            'title' => 'required',
            'description' => 'required',
            'priority' => 'required',
            'h-captcha-response' => 'recaptcha',
        ]);
        $executed = RateLimiter::attempt(
            'create-ticket:' . auth()->user()->id,
            $perMinute = 1,
            function () {
                return true;
            }
        );
        if (!$executed) {
            return redirect()->back()->with('error', 'You are sending too many messages. Please wait a few minutes and try again.');
        }
        $ticket = new Ticket();
        $ticket->title = request('title');
        $ticket->status = 'open';
        $ticket->client = auth()->user()->id;
        $ticket->priority = request('priority');
        $ticket->save();

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => request('description'),
            'user_id' => auth()->user()->id,
        ]);

        return redirect('/tickets')->with('success', 'Ticket created successfully');
    }

    public function show(Ticket $ticket)
    {
        $messages = TicketMessage::where('ticket_id', $ticket->id)->get();

        return view('clients.tickets.show', compact('ticket', 'messages'));
    }

    public function close(Ticket $ticket)
    {
        $ticket->status = 'closed';
        $ticket->save();

        return redirect('/tickets')->with('success', 'Ticket closed successfully');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'g-recaptcha-response' => 'recaptcha',
            'cf-turnstile-response' => 'recaptcha',
            'h-captcha-response' => 'recaptcha',
            'message' => 'required',
        ]);
        if ($ticket->status == 'closed') {
            return redirect()->back()->with('error', 'You can not reply to a closed ticket.');
        }
        $executed = RateLimiter::attempt(
            'send-message:' . $ticket->id,
            $perMinute = 3,
            function () {
                return true;
            }
        );
        if (!$executed) {
            return redirect()->back()->with('error', 'You are sending too many messages. Please wait a few minutes and try again.');
        }
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => request('message'),
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->back()->with('success', 'Message sent successfully');
    }
}
