<div class="container mt-14">
    <x-navigation.breadcrumb />
    <div class="px-2">
        <h4 class="text-2xl font-bold pb-3">{{ __('account.credits') }}</h4>
        @if (Auth::user()->credits->count() > 0)
        <div class="mt-4 grid gap-4 md:grid-cols-3 mb-8">
            <div class="flex flex-col gap-1 bg-background-secondary p-4 rounded-lg">
                <span class="text-xl font-semibold">{{ __('account.credits') }}</span>
                <span class="text-gray-500">{{ __('account.current_balance') }}</span>
                <span class="text-2xl font-semibold mt-1">
                    <ul>
                        @foreach (Auth::user()->credits as $credit)
                        <li>{{ $credit->formattedAmount }} {{ $credit->currency->code }}</li>
                        @endforeach
                    </ul>
                </span>
            </div>
            <div class="flex flex-col gap-1 bg-background-secondary p-4 rounded-lg">
                <span class="text-xl font-semibold">{{ __('account.total_added') }}</span>
                <span class="text-gray-500">{{ __('account.total_added_description') }}</span>
                <span class="text-2xl font-semibold mt-1 text-gray-500">
                    <ul>
                        @forelse ($totalAdded as $currencyCode => $amount)
                        <li>{{ number_format($amount, 2) }} {{ $currencyCode }}</li>
                        @empty
                        <li>-</li>
                        @endforelse
                    </ul>
                </span>
            </div>
            <div class="flex flex-col gap-1 bg-background-secondary p-4 rounded-lg">
                <span class="text-xl font-semibold">{{ __('account.total_spent') }}</span>
                <span class="text-gray-500">{{ __('account.total_spent_description') }}</span>
                <span class="text-2xl font-semibold mt-1 text-gray-500">
                    <ul>
                        @forelse ($totalSpent as $currencyCode => $amount)
                        <li>{{ number_format($amount, 2) }} {{ $currencyCode }}</li>
                        @empty
                        <li>-</li>
                        @endforelse
                    </ul>
                </span>
            </div>
        </div>
        @else
        <p class="mb-8">{{ __('account.no_credit') }}</p>
        @endif

        <h4 class="text-xl font-bold pb-3">{{ __('account.add_credit') }}</h4>

        <form wire:submit.prevent="addCredit">
            <!-- Currency and amount -->
            <div class="grid grid-cols-2 gap-4">
                <x-form.select name="currency" :label="__('account.input.currency')" wire:model.live="currency" required>
                    @foreach(\App\Models\Currency::all() as $currency)
                    <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                    @endforeach
                </x-form.select>
                <x-form.input x-mask:dynamic="$money($input, '.', '', 2)" name="amount" type="number"
                    :label="__('account.input.amount')" :placeholder="__('account.input.amount_placeholder')"
                    wire:model.live.debounce.250ms="amount" required />

                <x-form.select name="gateway" :label="__('product.payment_method')" wire:model.live="gateway" required>
                    @foreach($gateways as $gatewayy)
                    <option value="{{ $gatewayy->id }}" wire:key="{{ $gatewayy->id }}" @if($gatewayy->id == $gateway) selected @endif>{{ $gatewayy->name }}</option>
                    @endforeach
                </x-form.select>
            </div>


            <x-button.primary type="submit" class="w-full mt-4">
                {{ __('account.add_credit') }}
            </x-button.primary>
        </form>
    </div>
</div>