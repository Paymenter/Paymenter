<div class="w-fit mr-2 mb-2 md:mb-0">
    @if(count($currencies) > 1)
        <select name="currency" id="currency" :label="__('Currency')" wire:model.live="currentCurrency" class="w-fit bg-primary-800 text-white border border-primary-700 rounded-md p-2">
            @foreach ($currencies as $currency)
                <option value="{{ $currency->code }}" {{ $currency->code == $currentCurrency ? 'selected' : '' }}>{{ $currency->code }}</option>
            @endforeach
        </select>
    @endif
</div>