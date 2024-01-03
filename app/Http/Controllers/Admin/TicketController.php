<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploadHelper;
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
        return view('admin.tickets.index');
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
            'attachments' => 'nullable|array',
        ]);

        if ($ticket->status == 'closed') {
            return redirect()->route('admin.tickets.show', $ticket)->with('error', 'Ticket is closed');
        }

        $ticket->status = 'replied';
        $ticket->save();
        $ticketMessage = $ticket->messages()->create([
            'user_id' => auth()->user()->id,
            'message' => $request->get('message'),
        ]);

        if($request->hasFile('attachments')) {
            foreach($request->file('attachments') as $file) {
                FileUploadHelper::upload($file, $ticketMessage, TicketMessage::class);
            }
        }

        NotificationHelper::sendNewTicketMessageNotification($ticket, User::where('id', $ticket->user_id)->first());

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Message has been sent');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'title' => 'required|string',
            'status' => 'required|in:open,closed',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'product_id' => 'nullable|exists:order_products,id',
        ]);

        $ticket->title = $request->get('title');
        $ticket->status = $request->get('status');
        $ticket->priority = $request->get('priority');
        $ticket->assigned_to = $request->get('assigned_to');
        $ticket->order_id = $request->get('product_id');
        $ticket->save();

        return redirect()->route('admin.tickets.show', $ticket)->with('success', 'Ticket status has been updated');
    }
}
