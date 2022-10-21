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

    function index()
    {
        $tickets = Tickets::where('client', auth()->user()->id)->get();
        return view('tickets.index', compact('tickets'));
    }

    function create()
    {
        return view('tickets.create');
    }

    function store(Request $request)
    {
        if (Settings::first()->recaptcha == 1) {
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

    function show($id)
    {
        $ticket = Tickets::find($id);
        return view('tickets.show', compact('ticket'));
    }

    function close($id)
    {
        $ticket = Tickets::find($id);
        $ticket->status = 'closed';
        $ticket->save();
        return redirect('/tickets')->with('success', 'Ticket closed successfully');
    }
}
