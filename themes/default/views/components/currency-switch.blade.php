{{-- @deprecated, removed in 1.6 --}}
<div>
    <strong class="block p-2 text-xs font-semibold uppercase text-base/50"> {{ __('account.input.currency') }} </strong>
    <x-select wire:model.live="currentCurrency" :options="$this->currencies" placeholder="{{ __('common.select.placeholder', ['name' => strtolower(__('account.input.currency'))]) }}" />
</div>
