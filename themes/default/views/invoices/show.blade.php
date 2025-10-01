<div @if ($checkPayment) wire:poll.5s="checkPaymentStatus" @endif>
    @if ($this->pay)
    <x-modal
        :title="config('settings.invoice_proforma', false) ? __('invoices.payment_for_proforma_invoice', ['id' => $invoice->id]) : __('invoices.payment_for_invoice', ['number' => $invoice->number])"
        open>
        <div class="mt-8">
            {{ $this->pay }}
        </div>
        <x-slot name="closeTrigger">
            <div class="flex gap-4">
                Amount: {{ $invoice->formattedRemaining }}
                <button wire:confirm="Are you sure?" wire:click="exitPay" @click="open = false"
                    class="text-primary-100">
                    <x-ri-close-fill class="size-6" />
                </button>
            </div>
        </x-slot>
    </x-modal>
    @endif
    @if($showPayModal && !$this->pay)
    <x-modal
        :title="config('settings.invoice_proforma', false) ? __('invoices.payment_for_proforma_invoice', ['id' => $invoice->id]) : __('invoices.payment_for_invoice', ['number' => $invoice->number])"
        open>
        <x-slot name="closeTrigger">
            <div class="flex gap-4">
                Amount: {{ $invoice->formattedRemaining }}
                <button wire:confirm="Are you sure?" wire:click="exitPay" @click="open = false"
                    class="text-primary-100">
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
                                <span wire:loading.remove
                                    wire:target="payWithSavedMethod('{{ $method->ulid }}')">Pay</span>
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
                    <a href="{{ route('account.payment-methods', ['currency' => $invoice->currency_code]) }}"
                        class="w-fit">
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
                                    <span wire:loading
                                        wire:target="payWithMethod('{{ $method->id }}')">Processing...</span>
                                    <span wire:loading.remove
                                        wire:target="payWithMethod('{{ $method->id }}')">Pay</span>
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
    @endif

    <div class="flex justify-end">
        <div class="max-w-[200px] w-full text-right">
            <span class="cursor-pointer text-base underline" wire:click="downloadPDF">
                <span wire:loading wire:target="downloadPDF">
                    <x-ri-loader-5-fill class="size-6 animate-spin" />
                </span>
                <span wire:loading.remove wire:target="downloadPDF">Download PDF</span>
            </span>
        </div>
    </div>

    <div class="bg-background-secondary border border-neutral p-12 rounded-lg mt-2">
        <h1 class="text-2xl font-bold sm:text-3xl">
            {{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id'
            => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}
        </h1>
        <div class="sm:flex justify-between pr-4 pt-4">
            <div class="mt-4 sm:mt-0">
                <p class="uppercase font-bold">{{ __('invoices.issued_to') }}</p>
                <p>{{ $invoice->user_name }}</p>
                @foreach($invoice->user_properties as $property)
                <p>{{ $property }}</p>
                @endforeach
            </div>
            <div class="mt-4 sm:mt-0 text-right">
                <p class="uppercase font-bold">{{ __('invoices.bill_to') }}</p>
                <p>{!! nl2br(e($invoice->bill_to)) !!}</p>
            </div>
        </div>
        <div class="sm:flex justify-between pr-4 pt-4 mt-6">
            <div class="">
                <p class="text-base">{{ !$invoice->number && config('settings.invoice_proforma', false) ?
                    __('invoices.proforma_invoice_date') : __('invoices.invoice_date') }}: {{
                    $invoice->created_at->format('d M Y') }}</p>
                @if($invoice->due_at)
                <p class="text-base">{{ __('invoices.due_date') }}: {{ $invoice->due_at->format('d M Y') }}</p>
                @endif
                @if($invoice->number)
                <p class="text-base">{{ __('invoices.invoice_no')}}: {{ $invoice->number }}</p>
                @endif
            </div>
            <div class="max-w-[300px] w-full">
                @if ($invoice->status == 'paid')
                <div class="text-green-500 mt-6 text-lg text-center font-semibold">
                    {{ __('invoices.paid') }}
                </div>
                @elseif ($invoice->status == 'pending')
                @if($checkPayment || $invoice->transactions->where('status',
                \App\Enums\InvoiceTransactionStatus::PROCESSING)->where('created_at', '>=', now()->subDays(1))->count()
                > 0)
                <div class="text-yellow-500 mb-6 text-lg text-center flex items-center justify-center">
                    {{ __('invoices.payment_processing') }}
                    <x-ri-loader-5-fill aria-hidden="true" class="size-6 ms-2 fill-yellow-600 animate-spin" />
                </div>
                @else
                <div class="mb-6 text-lg text-center">
                    @if($invoice->transactions->where('status',
                    \App\Enums\InvoiceTransactionStatus::PROCESSING)->count() > 0)
                    <span class="text-yellow-500">{{ __('invoices.payment_processing') }}</span>
                    <p class="text-sm">{{ __('invoices.duplicate_payment') }}</p>
                    @else
                    <span class="text-yellow-500">{{ __('invoices.payment_pending') }}</span>
                    @endif
                </div>
                <x-button.primary wire:click="$set('showPayModal', true)" class="mt-2" wire:loading.attr="disabled"
                    wire:target="$set('showPayModal')">
                    <span wire:loading wire:target="pay">Processing...</span>
                    <span wire:loading.remove wire:target="pay">Pay</span>
                </x-button.primary>
                @endif
                @endif
            </div>
        </div>

        <div class="mt-12 border-b border-neutral overflow-x-auto">
            <table class="w-full">
                <thead class="bg-background border border-neutral rounded-lg">
                    <tr>
                        <th scope="col"
                            class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-l-lg">
                            {{ __('invoices.item') }}
                        </th>
                        <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                            {{ __('invoices.price') }}
                        </th>
                        <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                            {{ __('invoices.quantity') }}
                        </th>
                        <th scope="col"
                            class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-r-lg">
                            {{ __('invoices.total') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                    <tr>
                        <td class="p-4 font-normal whitespace-nowrap">
                            @if(in_array($item->reference_type, ['App\Models\Service', 'App\Models\ServiceUpgrade']))
                            <a href="{{ route('services.show', $item->reference_type == 'App\Models\Service' ? $item->reference_id : $item->reference->service_id) }}"
                                class="hover:underline underline-offset-2">{{ $item->description }}
                            </a>
                            @else
                            {{ $item->description }}
                            @endif
                        </td>
                        <td class="p-4 font-normal whitespace-nowrap text-base">{{ $item->formattedPrice }}
                        </td>
                        <td class="p-4 font-normal whitespace-nowrap">{{ $item->quantity }}</td>
                        <td class="p-4 whitespace-nowrap font-semibold">{{ $item->formattedTotal }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="space-y-3 sm:text-right sm:ml-auto sm:w-72 mt-10">
            @if ($invoice->formattedTotal->tax > 0)
            <div class="flex justify-between">
                <div class="text-sm font-medium text-gray-500 uppercase dark:text-base">{{ __('invoices.subtotal') }}
                </div>
                <div class="text-base font-medium text-gray-900 dark:text-white">
                    {{ $invoice->formattedTotal->format($invoice->formattedTotal->subtotal) }}
                </div>
            </div>
            <div class="flex justify-between">
                <div class="text-sm font-medium text-gray-500 uppercase dark:text-base">
                    {{ $invoice->tax->name }} ({{ $invoice->tax->rate }}%)
                </div>
                <div class="text-base font-medium text-gray-900 dark:text-white">
                    {{ $invoice->formattedTotal->formatted->tax }}
                </div>
            </div>
            @endif
            <div class="flex justify-between">
                <div class="text-base font-semibold text-gray-900 uppercase dark:text-white">Total</div>
                <div class="text-base font-bold text-gray-900 dark:text-white">
                    {{ $invoice->formattedTotal }}
                </div>
            </div>
        </div>

        @if ($invoice->transactions->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-2xl font-bold">{{ __('invoices.transactions') }}</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border border-neutral rounded-lg">
                        <tr>
                            <th scope="col"
                                class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-l-lg">
                                {{ __('invoices.date') }}
                            </th>
                            <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                                {{ __('invoices.transaction_id') }}
                            </th>
                            <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                                {{ __('invoices.gateway') }}
                            </th>
                            <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                                {{ __('invoices.amount') }}
                            </th>
                            <th scope="col"
                                class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-r-lg">
                                {{ __('invoices.status') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->transactions->sortByDesc('created_at') as $transaction)
                        <tr>
                            <td class="p-4 font-normal whitespace-nowrap">
                                {{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->transaction_id }}
                            </td>
                            <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->gateway?->name }}
                            </td>
                            <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->formattedAmount }}
                            </td>
                            <td class="p-4 font-normal whitespace-nowrap">
                                @if($transaction->status == \App\Enums\InvoiceTransactionStatus::SUCCEEDED)
                                <span class="text-green-600 font-semibold">{{
                                    __('invoices.transaction_statuses.succeeded') }}</span>
                                @elseif($transaction->status == \App\Enums\InvoiceTransactionStatus::PROCESSING)
                                <span class="text-yellow-600 font-semibold flex items-center">
                                    {{ __('invoices.transaction_statuses.processing') }}
                                    <x-ri-loader-5-fill aria-hidden="true"
                                        class="size-6 me-2 fill-yellow-600 animate-spin" />
                                </span>
                                @elseif($transaction->status == \App\Enums\InvoiceTransactionStatus::FAILED)
                                <span class="text-red-600 font-semibold">{{ __('invoices.transaction_statuses.failed')
                                    }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

</div>