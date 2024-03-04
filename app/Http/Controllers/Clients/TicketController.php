<?php

namespace App\Http\Controllers\Clients;

use App\Helpers\FileUploadHelper;
use App\Helpers\NotificationHelper;
use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Http\Controllers\Controller;
use App\Validators\ReCaptcha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tickets = $user->tickets()->with(['messages', 'messages.user'])->get();
        $sort = $request->get('sort');

        return view(
            'clients.tickets.index',
            compact(
                'tickets',
                'user',
                'sort'
            )
        );
    }

    public function create()
    {
        $services = Auth::user()->orders;

        return view('clients.tickets.create', compact('services'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        (new ReCaptcha())->verify($request);
        $body = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'priority' => 'required',
        ]);

        if (RateLimiter::tooManyAttempts("create-ticket:$user->id", $perMinute = 1)) {
            return redirect()->back()->with('error', 'You are sending too many messages. Please wait a few minutes and try again.');
        }
        RateLimiter::hit("create-ticket:$user->id");

        $ticket = new Ticket();
        $ticket->title = $body['title'];
        $ticket->status = 'open';
        $ticket->user()->associate($user);
        $ticket->priority = $body['priority'];
        $ticket->save();

        $ticketMessage = new TicketMessage();
        $ticketMessage->ticket_id = $ticket->id;
        $ticketMessage->message = $body['description'];
        $ticketMessage->user()->associate($user);
        $ticketMessage->save();

        NotificationHelper::sendNewTicketNotification($ticket, $user);

        return redirect()->route('clients.tickets.show', $ticket)->with('success', 'Ticket has been created');
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id != Auth::user()->id) {
            return redirect()->back()->with('error', 'You do not have permission to view this ticket.');
        }

        return view('clients.tickets.show', compact('ticket'));
    }

    public function close(Ticket $ticket)
    {
        if ($ticket->status == 'closed') {
            return redirect()->back()->with('error', 'Ticket already closed.');
        }

        $ticket->status = 'closed';
        $ticket->save();

        return redirect('/tickets')->with('success', 'Ticket closed successfully');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        (new ReCaptcha())->verify($request);
        $request->validate([
            'message' => 'required',
            'attachments' => 'nullable|array',
        ]);

        if (RateLimiter::tooManyAttempts("send-message:$user->id", $perMinute = 5)) {
            return redirect()->route('clients.tickets.show', $ticket->id)->with('error', 'You are sending too many messages. Please wait a few minutes and try again.');
        }
        RateLimiter::hit("send-message:$user->id");

        $ticket->status = 'open';
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

        //NotificationHelper::sendNewTicketMessageNotification($ticket, $user);
        if ($ticket->assigned_to) {
            NotificationHelper::sendNewTicketMessageNotification($ticket, User::where('id', $ticket->assigned_to)->first());
        }

        return redirect()->back()->with('success', 'Message sent successfully');
    }
}
