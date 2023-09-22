# Your order has been received

Thanks for your order. We will process it as soon as possible.

@component('mail::table')
    | Product | Quantity | Price |
    |:--------|:---------|:------|
    @foreach ($products as $product)
        | {{ $product->product()->get()->first()->name }} | {{ $product->quantity }} |
        {{ config('settings::currency_sign') }} {{ $order->total() }} |
    @endforeach
@endcomponent

Total: {{ config('settings::currency_sign') }} {{ $order->total() }}

@component('mail::button', ['url' => route('clients.home')])
    View order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
