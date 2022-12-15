<?php

namespace App\Http\Controllers\Clients;

use App\Models\Orders;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tickets;
use App\Models\TicketMessages;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class TicketsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    function index(Request $request)
    {
        $tickets = Tickets::where('client', auth()->user()->id)->get();
        $users = User::where('id', auth()->user()->id)->get();
        $ticketMessages = TicketMessages::all();
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

    function create()
    {
        $services = Orders::where('client', auth()->user()->id)->get();
        return view('clients.tickets.create', compact('services'));
    }

    function store(Request $request)
    {
        if (config('settings::recaptcha') == 1) {
            $request->validate([
                'g-recaptcha-response' => 'required|recaptcha',
                'title' => 'required',
                'description' => 'required',
                'priority' => 'required',
            ]);
        } else {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'priority' => 'required',
            ]);
        }
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
        $ticket = new Tickets();
        $ticket->title = request('title');
        $ticket->status = 'open';
        $ticket->client = auth()->user()->id;
        $ticket->priority = request('priority');
        $ticket->save();

        TicketMessages::create([
            'ticket_id' => $ticket->id,
            'message' => request('description'),
            'user_id' => auth()->user()->id
        ]);


        return redirect('/tickets')->with('success', 'Ticket created successfully');
    }

    function show(Tickets $id)
    {
        $ticket = $id;
        $messages = TicketMessages::where('ticket_id', $id->id)->get();
        return view('clients.tickets.show', compact('ticket', 'messages'));
    }

    function close(Tickets $id)
    {
        $ticket = $id;
        $ticket->status = 'closed';
        $ticket->save();
        return redirect('/tickets')->with('success', 'Ticket closed successfully');
    }

    function reply(Request $request, Tickets $id)
    {
        if (config('settings::recaptcha') == 1) {
            $request->validate([
                'g-recaptcha-response' => 'required|recaptcha',
                'message' => 'required',
            ]);
        } else {
            $request->validate([
                'message' => 'required',
            ]);
        }
        if($id->status == 'closed'){
            return redirect()->back()->with('error', 'You can not reply to a closed ticket.');
        }
        $executed = RateLimiter::attempt(
            'send-message:' . $id->id,
            $perMinute = 3,
            function () {
                return true;
            }
        );
        if (!$executed) {
            return redirect()->back()->with('error', 'You are sending too many messages. Please wait a few minutes and try again.');
        }
        TicketMessages::create([
            'ticket_id' => $id->id,
            'message' => request('message'),
            'user_id' => auth()->user()->id
        ]);
        return redirect()->back()->with('success', 'Message sent successfully');
    }
}
