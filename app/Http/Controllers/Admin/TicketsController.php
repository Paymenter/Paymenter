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

    function edit($id)
    {
        $ticket = Tickets::find($id);
        return view('admin.tickets.edit', compact('ticket'));
    }

    function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => 'required',
            'client' => 'required'
        ]);

        $ticket = Tickets::find($id);
        $ticket->title = $request->get('title');
        $ticket->description = $request->get('description');
        $ticket->status = $request->get('status');
        $ticket->client = $request->get('client');
        $ticket->save();

        return redirect('/admin/tickets')->with('success', 'Ticket has been updated');
    }

    function destroy($id)
    {
        $ticket = Tickets::find($id);
        $ticket->delete();

        return redirect('/admin/tickets')->with('success', 'Ticket has been deleted Successfully');
    }
}
