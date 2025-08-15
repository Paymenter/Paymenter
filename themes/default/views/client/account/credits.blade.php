<div>
    <x-navigation.breadcrumb />
    <div class="px-2">
        <h4 class="text-2xl font-bold pb-3">{{ __('account.credits') }}</h4>
        @if (Auth::user()->credits->count() > 0)
        <div class="flex flex-wrap gap-4">
            @foreach (Auth::user()->credits as $credit)
            <div class="flex flex-col bg-primary-700 w-fit rounded-lg px-5 p-3 items-center gap-1">
                <h5 class="text-lg font-bold">{{ $credit->currency->code }}</h5>
                <p class="text-primary-100">{{ $credit->formattedAmount }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p>{{ __('account.no_credit') }}</p>
        @endif

        <h4 class="text-xl font-bold pb-3">{{ __('account.add_credit') }}</h4>

        <form wire:submit.prevent="addCredit">
            <!-- Currency and amount -->
            <div class="grid grid-cols-2 gap-4">
                <x-form.select name="currency" :label="__('account.input.currency')" wire:model="currency" required>
                    @foreach(\App\Models\Currency::all() as $currency)
                    <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                    @endforeach
                </x-form.select>
                <x-form.input x-mask:dynamic="$money($input, '.', '', 2)" name="amount" type="number"
                    :label="__('account.input.amount')" :placeholder="__('account.input.amount_placeholder')"
                    wire:model="amount" required />

                <x-form.select name="gateway" :label="__('product.payment_method')" wire:model="gateway" required>
                    @foreach(\App\Models\Gateway::all() as $gateway)
                    <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                    @endforeach
                </x-form.select>
            </div>


            <x-button.primary type="submit" class="w-full mt-4">
                {{ __('account.add_credit') }}
            </x-button.primary>
        </form>

        <!-- Auto-renew toggle button styled as a green button when enabled -->
        <div class="mt-6 flex items-center gap-2">
            <button
                type="button"
                wire:click="toggleAutoRenewCredits"
                class="px-4 py-1.5 rounded font-semibold flex items-center gap-2 transition duration-300 cursor-pointer
                    {{ $auto_renewal_enabled ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-background-secondary text-base border border-neutral hover:bg-background-secondary/80' }}">
                @if($auto_renewal_enabled)
                    <x-ri-check-line class="size-5" />
                    {{ __('Auto Renew Enabled') }}
                @else
                    {{ __('Enable Auto Renew') }}
                @endif
            </button>
            <!-- Info tooltip -->
            <div x-data="{ open: false }" class="relative">
                <button type="button" @mouseenter="open = true" @mouseleave="open = false" class="text-base/50 cursor-pointer">
                    <x-ri-information-line class="size-5" />
                </button>
                <div x-show="open" x-transition class="absolute left-6 top-1 z-10 bg-background-secondary border border-neutral rounded px-3 py-2 text-xs text-base w-64 shadow-lg" style="white-space: normal;">
                    {{ __('When enabled, your invoices will be automatically paid using your available credits if possible.') }}
                </div>
            </div>
        </div>
    </div>
</div>