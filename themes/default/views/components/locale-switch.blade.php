<x-dropdown>
    <x-slot:trigger>
        <div class="text-sm text-base font-semibold text-nowrap">
            @if(count($locales) > 1)
            {{ config('app.available_locales')[app()->getLocale()] ?? app()->getLocale() }}
            @endif
            @if(count($locales) > 1 && count($this->currencies) > 1 && Cart::items()->isEmpty())
            <span class="text-base/50 font-semibold">|</span>
            @endif
            @if(count($this->currencies) > 1 && Cart::items()->isEmpty())
            {{ collect($this->currencies)->firstWhere('value', $this->currentCurrency)['label'] ?? $this->currentCurrency }}
            @endif
        </div>
    </x-slot:trigger>
    <x-slot:content>
        @if(count($locales) > 1)
        <div>
            <strong class="block p-2 text-xs font-semibold uppercase text-base/50"> {{ __('general.language') }} </strong>
            <x-select wire:model.live="currentLocale" :options="collect($locales)->map(fn($code) => [
                'value' => $code,
                'label' => config('app.available_locales')[$code] ?? $code
                ])->values()->toArray()" :placeholder="__('general.select_language')" />
        </div>
        @endif
        @if(count($this->currencies) > 1 && Cart::items()->isEmpty())
        <div>
            <strong class="block p-2 text-xs font-semibold uppercase text-base/50"> {{ __('account.input.currency') }} </strong>
            <x-select wire:model.live="currentCurrency" :options="$this->currencies" placeholder="{{ __('common.select.placeholder', ['name' => strtolower(__('account.input.currency'))]) }}" />
        </div>
        @endif
    </x-slot:content>
</x-dropdown>
