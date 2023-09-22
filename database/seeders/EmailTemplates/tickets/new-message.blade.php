# New reply

New reply has been added to ticket.

Ticket subject: {{ $ticket->subject }}

Ticket message: {{ $ticket->messages()->latest()->first()->message }}

@component('mail::button', ['url' => route('clients.tickets.show', $ticket)])
    View ticket
@endcomponent

Thanks, <br>
{{ config('app.name') }}