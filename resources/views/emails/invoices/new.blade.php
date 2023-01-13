@component('mail::message')
# New invoice

There is a new invoice for you.
 
Pay before: {{ $invoice->order()->first()->expiry_date }} or your service will be suspended.

You need to pay: {{ config('settings::currency_sign') }} {{ $invoice->order()->first()->total }} 

@component('mail::button', ['url' => route('clients.invoice.show', $invoice)])
Pay now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
