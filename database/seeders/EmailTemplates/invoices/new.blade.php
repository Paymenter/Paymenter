# New invoice
There is a new invoice for you.
You need to pay: {{ config('settings::currency_sign') }} {{ $invoice->total() }}
@component('mail::table')
| Product | Price |
|:--------|:------|
@foreach ($products as $product)
| {{ $product->description }} | {{ config('settings::currency_sign') }} {{ $product->total }} |
@endforeach
@endcomponent
@component('mail::button', ['url' => route('clients.invoice.show', $invoice)])
Pay now
@endcomponent
Thanks,<br>
{{ config('app.name') }}
