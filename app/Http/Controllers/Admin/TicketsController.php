<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tickets;

class TicketsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth.admin');
    }

    function index()
    {
        $tickets = Tickets::where('status', '!=', 'closed')->get();
        return view('admin.tickets.index', compact('tickets'));
    }

    function create()
    {
        return view('admin.tickets.create');
    }

    function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
            'client' => 'required'
        ]);

        $ticket = new Tickets([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'status' => $request->get('status'),
            'client' => $request->get('client')
        ]);
        $ticket->save();
        return redirect('/admin/tickets')->with('success', 'Ticket has been added');
    }

    function show($id)
    {
        $ticket = Tickets::find($id);
        if(!$ticket) {
            return abort(404);
        }
        return view('admin.tickets.show', compact('ticket'));
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
