
# New ticket

New ticket has been created.

Ticket subject: {{ $ticket->subject }}

Ticket message: {{ $ticket->messages()->first()->message }}

@component('mail::button', ['url' => route('clients.tickets.show', $ticket)])
    View ticket
@endcomponent

Thanks, <br>
{{ config('app.name') }}
