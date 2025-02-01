<x-select
    wire:model.live="currentCurrency"
    :options="collect($currencies)->map(fn($currency) => [
        'value' => $currency->code,
        'label' => $currency->code
    ])->values()->toArray()"
    placeholder="Select currency"
/>
