<div>
    <strong class="block p-2 text-xs font-semibold uppercase text-base/50"> Currency </strong>
    <x-select wire:model.live="currentCurrency" :options="$this->currencies" placeholder="Select currency" />
</div>