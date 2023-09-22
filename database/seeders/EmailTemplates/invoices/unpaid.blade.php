# Unpaid invoice
You have an unpaid invoice
You need to pay: {{ config('settings::currency_sign') }} {{ $invoice->total() }}<br>
Your services have been blocked, will be removed in {{ config('settings::remove_unpaid')?? 7 }} days
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

