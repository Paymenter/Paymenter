<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Tickets;
use Illuminate\Http\Request;
use App\Models\TicketMessages;
use App\Http\Controllers\Controller;

class TicketsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    public function index()
    {
        $tickets = Tickets::where('status', '!=', 'closed')->get();

        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $users = User::all();

        return view('admin.tickets.create', compact('users'));
    }

    public function store(Request $request)
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
            'client' => $request->get('user'),
        ]);
        $ticket->save();

        TicketMessages::create([
            'ticket_id' => $ticket->id,
            'message' => $request->get('description'),
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->back()->with('success', 'Ticket created successfully');
    }

    public function show(Tickets $ticket)
    {
        $ticket = Tickets::find($id);
        if (!$ticket) {
            return abort(404);
        }

        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Tickets $ticket)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $ticket->status = 'replied';
        $ticket->save();
        $ticket->messages()->create([
            'user_id' => auth()->user()->id,
            'message' => $request->get('message'),
        ]);

        return redirect()->back()->with('success', 'Reply has been sent');
    }

    public function status(Request $request, Tickets $ticket)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $ticket->status = $request->get('status');
        $ticket->save();

        return redirect()->back()->with('success', 'Status has been changed');
    }
}
