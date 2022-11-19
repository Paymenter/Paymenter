<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\Tickets;
use App\Models\Statistics;
use App\Models\TicketMessages;

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
            'tickets.index',
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
        return view('tickets.create', compact('services'));
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
        $ticket = new Tickets();
        $ticket->title = request('title');
        $ticket->status = 'open';
        $ticket->client = auth()->user()->id;
        $ticket->priority = request('priority');
        $ticket->save();

        Statistics::updateOrCreate(
            [
                'name' => 'tickets',
                'date' => date('Y-m-d'),
            ]
        )->increment('value');

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
        return view('tickets.show', compact('ticket', 'messages'));
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
        TicketMessages::create([
            'ticket_id' => $id->id,
            'message' => request('message'),
            'user_id' => auth()->user()->id
        ]);
        return redirect()->back()->with('success', 'Message sent successfully');
    }
}
