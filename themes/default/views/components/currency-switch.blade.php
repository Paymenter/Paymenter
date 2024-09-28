<div>
    @if(count($currencies) > 1)
        <x-form.select name="currency" id="currency" :label="__('Currency')" wire:model.live="currentCurrency">
            @foreach ($currencies as $currency)
                <option value="{{ $currency->code }}" {{ $currency->code == $currentCurrency ? 'selected' : '' }}>{{ $currency->code }}</option>
            @endforeach
        </x-form.select>
    @endif
</div>