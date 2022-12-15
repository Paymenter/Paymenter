<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tickets;
use App\Models\User;
use App\Models\TicketMessages;

class TicketsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth.admin');
    }

    function index()
    {
        $tickets = Tickets::where('status', '!=', 'closed')->get();
        return view('admin.clients.tickets.index', compact('tickets'));
    }

    function create()
    {
        $users = User::all();
        return view('admin.clients.tickets.create', compact('users'));
    }

    function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'user' => 'required',
            'priority' => 'required',
        ]);

        $ticket = new Tickets([
            'title' => $request->get('title'),
            'status' => 'open',
            'priority' => $request->priority,
            'client' => $request->get('user')
        ]);
        $ticket->save();

        TicketMessages::create([
            'ticket_id' => $ticket->id,
            'message' => $request->get('description'),
            'user_id' => auth()->user()->id
        ]);

        return redirect()->back()->with('success', 'Ticket created successfully');
    }

    function show($id)
    {
        $ticket = Tickets::find($id);
        if (!$ticket) {
            return abort(404);
        }
        return view('admin.clients.tickets.show', compact('ticket'));
    }

    function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required'
        ]);

        $ticket = Tickets::find($id);
        $ticket->status = 'replied';
        $ticket->save();
        $ticket->messages()->create([
            'user_id' => auth()->user()->id,
            'message' => $request->get('message')
        ]);

        return redirect()->back()->with('success', 'Reply has been sent');
    }

    function status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $ticket = Tickets::find($id);
        $ticket->status = $request->get('status');
        $ticket->save();

        return redirect()->back()->with('success', 'Status has been changed');
    }
}
