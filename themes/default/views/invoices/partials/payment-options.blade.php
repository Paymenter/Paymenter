<x-modal
    :title="config('settings.invoice_proforma', false) ? __('invoices.payment_for_proforma_invoice', ['id' => $invoice->id]) : __('invoices.payment_for_invoice', ['number' => $invoice->number])"
    open>
    <x-slot name="closeTrigger">
        <div class="flex gap-4">
            Amount: {{ $invoice->formattedRemaining }}
            <button wire:confirm="Are you sure?" wire:click="exitPay" @click="open = false" class="text-primary-100">
                <x-ri-close-fill class="size-6" />
            </button>
        </div>
    </x-slot>
    <x-slot>
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
            <h3 class="text-lg font-semibold mb-1">
                @if($credit->amount >= $invoice->formattedRemaining->total)
                {{ __('invoices.pay_with_credits') }}
                @else
                {{ __('invoices.pay_with_credits') }} ({{ $credit->formattedAmount }})
                @endif
            </h3>
            <x-button.secondary wire:click="payWithCredit" class="h-fit !w-fit" wire:loading.attr="disabled">
                <x-loading target="payWithCredit" />
                <div wire:loading.remove wire:target="payWithCredit">
                    @if($credit->amount >= $invoice->formattedRemaining->total)
                    {{ __('invoices.pay_with_credits') }}
                    @else
                    {{ __('invoices.apply_credit', ['amount' => $credit->formattedAmount]) }}
                    @endif
                </div>
            </x-button.secondary>
        </div>
        @endif
        <!-- Sw saved payment methods if available -->
        @if($this->savedPaymentMethods)
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('account.saved_payment_methods') }}</h3>
            <!-- Show as *cards* -->
            @if($this->savedPaymentMethods->count() > 0)
            <div class="space-y-4">
                @foreach($this->savedPaymentMethods as $method)
                <div class="flex items-center justify-between p-4 border border-neutral rounded-lg">
                    <div class="flex items-center space-x-4">
                        <div class="text-xl">
                            @if($method->gateway->extension === 'Stripe')
                            <x-ri-bank-card-line class="size-6 text-primary" />
                            @elseif($method->gateway->extension === 'PayPal')
                            <x-ri-paypal-line class="size-6 text-primary" />
                            @else
                            <x-ri-secure-payment-line class="size-6 text-primary" />
                            @endif
                        </div>
                        <div>
                            <p class="font-medium">{{ $method->gateway->name }}</p>
                            <p class="text-sm text-base/50">
                                {{ $method->name }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <x-button.primary wire:click="payWithSavedMethod('{{ $method->ulid }}')"
                            wire:loading.attr="disabled">
                            <span wire:loading
                                wire:target="payWithSavedMethod('{{ $method->ulid }}')">Processing...</span>
                            <span wire:loading.remove wire:target="payWithSavedMethod('{{ $method->ulid }}')">Pay</span>
                        </x-button.primary>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-sm text-base/50 mb-4">{{ __('account.no_saved_payment_methods_description') }}</div>
            <!-- Add new payment method button -->
            @if(count($this->paymentMethods) > 0)
            <div class="mt-4">
                <a href="{{ route('account.payment-methods', ['currency' => $invoice->currency_code]) }}" class="w-fit">
                    <x-button.primary class="h-fit !w-fit">
                        <x-ri-add-line class="size-4 mr-2" />
                        {{ __('account.add_payment_method') }}
                    </x-button.primary>
                </a>
            </div>
            @endif
            @endif
        </div>
        @endif
        @if($this->gateways)
        <div class="mt-6">
            <div x-data="{ showOneTime: @if($this->savedPaymentMethods->count() == 0) true @else false @endif }"
                class="space-y-4">
                <button @click="showOneTime = !showOneTime"
                    class="flex items-center justify-between w-full p-3 text-left rounded-lg border border-neutral transition-colors">
                    <span class="text-sm font-medium">
                        {{ __('invoices.pay_with_one_time_method') }}
                    </span>
                    <x-ri-arrow-down-s-line class="size-5 text-gray-500 transition-transform duration-200"
                        x-bind:class="{ 'rotate-180': showOneTime }" />
                </button>

                <!-- Collapsible content -->
                <div x-show="showOneTime" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-4">
                    @foreach($this->gateways as $method)
                    <div class="flex items-center justify-between p-4 border border-neutral rounded-lg">
                        <div class="flex items-center space-x-4">
                            <p class="font-medium">{{ $method->name }}</p>
                        </div>
                        <div>
                            <x-button.primary wire:click="payWithMethod('{{ $method->id }}')"
                                wire:loading.attr="disabled">
                                <span wire:loading wire:target="payWithMethod('{{ $method->id }}')">Processing...</span>
                                <span wire:loading.remove wire:target="payWithMethod('{{ $method->id }}')">Pay</span>
                            </x-button.primary>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </x-slot>
</x-modal>