<div>
    <strong class="block p-2 text-xs font-semibold uppercase text-base/50"> Currency </strong>
    <x-select wire:model.live="currentCurrency" :options="collect($currencies)->map(fn($currency) => [
        'value' => $currency->code,
        'label' => $currency->code
    ])->values()->toArray()" placeholder="Select currency" />
</div>