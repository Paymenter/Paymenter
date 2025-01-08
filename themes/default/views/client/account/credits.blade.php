<div class="grid grid-cols-4">
    @include('client.account.navigation')


    <div class="col-span-3 flex flex-col gap-4">
        <div class="bg-primary-800 p-8 rounded-lg mt-2 col-span-3">

            <h4 class="text-2xl font-bold pb-3">{{ __('account.credits') }}</h4>
            @if(Auth::user()->credits->count() > 0)
                @foreach(Auth::user()->credits as $credit)
                    <div class="flex flex-col bg-primary-700 w-fit rounded-lg px-5 p-3 items-center gap-1">
                        <h5 class="text-lg font-bold">{{ $credit->currency->code }}</h5>
                        <p class="text-primary-100">{{ $credit->formattedAmount }}</p>
                    </div>
                @endforeach 
            @else 
                <p>{{ __('account.no_credit') }}</p>
            @endif
        </div>
        <div class="bg-primary-800 p-8 rounded-lg mt-2 col-span-3">
            <h4 class="text-xl font-bold pb-3">{{ __('account.add_credit') }}</h4>

            <form wire:submit.prevent="addCredit">
                <!-- Currency and amount -->
                <div class="grid grid-cols-2 gap-4">
                    <x-form.select name="currency" :label="__('account.input.currency')" wire:model="currency" required>
                        @foreach(\App\Models\Currency::all() as $currency)
                            <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input x-mask:dynamic="$money($input, '.', '', 2)" name="amount" type="number" :label="__('account.input.amount')" :placeholder="__('account.input.amount_placeholder')" wire:model="amount"
                        required />

                    <x-form.select name="gateway" label="Payment Gateway" wire:model="gateway" required>
                        @foreach(\App\Models\Gateway::all() as $gateway)
                            <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>


                <x-button.primary type="submit" class="w-full mt-4">
                    {{ __('account.add_credit') }}
                </x-button.primary>
            </form>

        </div>
    </div>
</div>