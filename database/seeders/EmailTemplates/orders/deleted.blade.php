# Your order has been deleted

Your order has been deleted and services blocked due to non-payment
@component('mail::table')
    | Product | Quantity |
    |:--------|:---------|
    @foreach ($products as $product)
        | {{ $product->product()->get()->first()->name }} | {{ $product->quantity }} |
    @endforeach
@endcomponent


@component('mail::button', ['url' => route('clients.tickets.index')])
    Do you consider it a mistake? Contact us
@endcomponent

Regards,<br>
{{ config('app.name') }}
