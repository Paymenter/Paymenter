@props(['amount' => null, 'showFree' => false])
@if ($amount == 0 && $showFree)
    Free
@else
    @if (config('settings::currency_position') == 'left')
        {{ config('settings::currency_sign') }}
    @endif
    {{ number_format($amount, 2) }}
    @if (config('settings::currency_position') == 'right')
        {{ config('settings::currency_sign') }}
    @endif
@endif
