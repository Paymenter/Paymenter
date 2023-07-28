<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Http\Controllers\Controller;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('status', '!=', 'closed')->get();
        $closed = Ticket::where('status', 'closed')->get();

        return view('admin.tickets.index', compact('tickets', 'closed'));
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

        $ticket = new Ticket([
            'title' => $request->get('title'),
            'status' => 'open',
            'priority' => $request->priority,
            'user_id' => $request->get('user'),
        ]);
        $ticket->save();

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $request->get('description'),
            'user_id' => auth()->user()->id,
        ]);
        NotificationHelper::sendNewTicketNotification($ticket, User::where('id', $ticket->user_id)->first());

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Ticket has been created');
    }

    public function show(Ticket $ticket)
    {
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
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

        NotificationHelper::sendNewTicketMessageNotification($ticket, User::where('id', $ticket->user_id)->first());

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Message has been sent');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,closed',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'product_id' => 'nullable|exists:order_products,id',
        ]);

        $ticket->status = $request->get('status');
        $ticket->priority = $request->get('priority');
        $ticket->assigned_to = $request->get('assinged_to');
        $ticket->order_id = $request->get('product_id');
        $ticket->save();

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Ticket status has been updated');
    }
}
