<div>
    <!-- Show apply credits button if available -->
    @php
    $credit = Auth::user()->credits()
    ->where('currency_code', $invoice->currency_code)
    ->where('amount', '>', 0)
    ->first();
    $itemHasCredit = $invoice->items()->where('reference_type', App\Models\Credit::class)->exists();
    @endphp
    @if($credit && !$itemHasCredit)
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">{{ __('invoices.pay_with_credits') }}</h3>
        <div wire:click="$set('selectedMethod', 'credit')"
            wire:loading.class="opacity-50 pointer-events-none" wire:target="selectedMethod,processPayment"
            class="flex items-center justify-between p-4 bg-background-secondary border border-neutral rounded-lg cursor-pointer transition-all {{ $selectedMethod === 'credit' ? 'border-primary ring-2 ring-primary' : 'border-neutral hover:border-neutral-focus' }}">
            <div class="flex items-center space-x-4">
                <div class="text-xl">
                    <x-ri-copper-coin-line class="size-6 text-primary" />
                </div>
                <div>
                    <p class="font-medium">{{ __('invoices.account_credits') }}</p>
                    <p class="text-sm text-base/50">{{ __('invoices.available_credits', ['amount' => $credit->formattedAmount]) }}</p>
                </div>
            </div>
            <div
                class="size-5 rounded-full bg-background-secondary border border-neutral flex items-center justify-center {{ $selectedMethod === 'credit' ? 'border-primary bg-primary' : 'border-neutral-focus' }}">
                @if($selectedMethod === 'credit')
                <x-ri-check-line class="size-4 text-white" /> @endif
            </div>
        </div>
    </div>
    @endif

    @if($this->savedPaymentMethods)
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">{{ __('account.saved_payment_methods') }}</h3>
        <div class="space-y-3">
            @foreach($this->savedPaymentMethods as $method)
            <div wire:click="$set('selectedMethod', '{{ $method->ulid }}')"
                class="flex items-center justify-between p-4 bg-background-secondary border rounded-lg cursor-pointer transition-all
                    {{ $selectedMethod === $method->ulid ? 'border-primary ring-2 ring-primary' : 'border-neutral hover:border-neutral-focus' }}"
                    wire:loading.class="opacity-50 pointer-events-none" wire:target="selectedMethod,processPayment">
                <div class="flex items-center gap-4">
                    <div>
                        @switch(strtolower($method->type))
                        @case('visa')
                        <x-icons.visa class="size-9" /> @break
                        @case('mastercard')
                        <x-icons.mastercard class="size-9" /> @break
                        @case('amex')
                        <x-icons.amex class="size-9" /> @break
                        @case('american express')
                        <x-icons.american-express class="size-9" /> @break
                        @case('discover')
                        <x-icons.discover class="size-9" /> @break
                        @case('paypal')
                        <x-icons.paypal class="size-9" /> @break
                        @case('sepa_debit')
                        <x-icons.sepa class="size-9" /> @break
                        @case('ideal')
                        <x-icons.ideal class="size-9" /> @break
                        @case('bancontact')
                        <x-icons.bancontact class="size-9" /> @break
                        @case('sofort')
                        <x-icons.sofort class="size-9" /> @break
                        @case('us_bank_account')
                        @case('bacs_debit')
                        @case('au_becs_debit')
                        <x-icons.bank-debit class="size-9" /> @break
                        @default
                        <x-ri-bank-card-line class="size-6 text-primary" />
                        @endswitch
                    </div>
                    <div>
                        <div class="font-semibold text-base"> {{ $method->name }} </div>
                        @if($method->expiry)
                        <div class="text-sm text-neutral-500">
                            {{ __('account.expires', ['date' => \Carbon\Carbon::parse($method->expiry)->format('m/Y')]) }}
                        </div>
                        @endif
                    </div>
                </div>
                <div
                    class="size-5 rounded-full border border-neutral flex items-center justify-center
                                    {{ $selectedMethod === $method->ulid ? 'border-primary bg-primary' : 'border-neutral-focus' }}">
                    @if($selectedMethod === $method->ulid)
                    <x-ri-check-line class="size-4 text-white" /> @endif
                </div>
            </div>
            @endforeach
            <a href="{{ route('account.payment-methods') }}" wire:navigate>
                <x-button.secondary>
                    <x-ri-add-line class="size-4" />
                    {{ __('account.add_payment_method') }}
                </x-button.secondary>
            </a>
        </div>
    </div>
    @endif

    @if($this->gateways && count($this->gateways) > 0)
    <div class="mb-6" x-data="{ showOneTime: @if($this->savedPaymentMethods->count() == 0) true @else false @endif }">
        <button @click="showOneTime = !showOneTime"
            class="flex items-center justify-between w-full p-3 text-left rounded-lg border border-neutral transition-colors @if($this->savedPaymentMethods->count() == 0) hidden @endif">
            <span class="text-sm font-medium">
                {{ __('invoices.pay_with_one_time_method') }}
            </span>
            <x-ri-arrow-down-s-line class="size-5 text-gray-500 transition-transform duration-200"
                x-bind:class="{ 'rotate-180': showOneTime }" />
        </button>
        <div class="space-y-3 mt-3" x-show="showOneTime" x-transition>
            @foreach($this->gateways as $method)
            <div wire:click="$set('selectedMethod', 'gateway-{{ $method->id }}')"
                class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all {{ $selectedMethod === 'gateway-' . $method->id ? 'border-primary ring-2 ring-primary' : 'border-neutral hover:border-neutral-focus' }}"
                wire:loading.class="opacity-50 pointer-events-none" wire:target="selectedMethod,processPayment">
                <div class="flex items-center space-x-4">
                    <div
                        class="bg-background-secondary border border-neutral rounded-lg overflow-hidden flex items-center justify-center h-9 w-9">
                        @if($method->meta?->icon)
                        <img src="{{ $method->meta->icon }}" alt="{{ $method->name }} Icon"
                            class="h-9 w-9 object-contain" />
                        @else
                        <x-ri-secure-payment-line class="size-5 text-primary" />
                        @endif
                    </div>
                    <div>
                        <p class="font-medium">{{ $method->name }}</p>
                        <p class="text-sm text-base/50">{{ __('invoices.one_time_payment') }}</p>
                    </div>
                </div>
                <div
                    class="size-5 rounded-full bg-background-secondary border border-neutral flex items-center justify-center {{ $selectedMethod === 'gateway-' . $method->id ? 'border-primary bg-primary' : 'border-neutral-focus' }}">
                    @if($selectedMethod === 'gateway-' . $method->id)
                    <x-ri-check-line class="size-4 text-white" /> @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($selectedMethod && $selectedMethod !== 'credit' && !str_starts_with($selectedMethod, 'gateway-') && $this->recurringServices()->exists())
    <div class="mt-4 p-2">
        <x-form.toggle :label="__('invoices.use_for_recurring')" wire:model.live="setAsDefault" />
    </div>
    @endif

    <div class="mt-6">
        <x-button.primary class="w-full" wire:click="processPayment" wire:loading.attr="disabled"
            :disabled="!$selectedMethod">
            <x-loading target="processPayment" />
            <div wire:loading.remove wire:target="processPayment">
                @if($selectedMethod === 'credit' && $credit && $credit->amount >= $invoice->formattedRemaining->total)
                {{ __('invoices.apply_credits_and_pay') }}
                @elseif($selectedMethod === 'credit' && $credit)
                {{ __('invoices.apply_credit_and_continue', ['amount' => $credit->formattedAmount]) }}
                @else
                {{ __('invoices.pay_now', ['amount' => $invoice->formattedRemaining]) }}
                @endif
            </div>
        </x-button.primary>
    </div>
</div>