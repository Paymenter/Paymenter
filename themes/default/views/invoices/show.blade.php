<div @if ($checkPayment) wire:poll="checkPaymentStatus" @endif>
    @if ($this->pay)
        <x-modal title="Payment for Invoice #{{ $invoice->number }}" open>
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
        <h1 class="text-2xl font-bold sm:text-3xl">{{ __('invoices.invoice', ['id' => $invoice->number]) }}</h1>
        <div class="sm:flex justify-between pr-4 pt-4">
            <div class="mt-4 sm:mt-0">
                <p class="uppercase font-bold">{{ __('invoices.issued_to') }}</p>
                <p>{{ $invoice->user->name }}</p>
                @foreach($invoice->user->properties()->with('parent_property')->whereHas('parent_property', function ($query) {
                    $query->where('show_on_invoice', true);
                })->get() as $property)
                    <p>{{ $property->value }}</p>
                @endforeach
            </div>
            <div class="mt-4 sm:mt-0 text-right">
                <p class="uppercase font-bold">{{ __('invoices.bill_to') }}</p>
                <p>{!! nl2br(e(config('settings.bill_to_text', config('settings.company_name')))) !!}</p>
            </div>
        </div>
        <div class="sm:flex justify-between pr-4 pt-4 mt-6">
            <div class="">
                <p class="text-base">{{ __('invoices.invoice_date')}}: {{ $invoice->created_at->format('d M Y') }}</p>
                @if($invoice->due_at)
                    <p class="text-base">{{ __('invoices.due_date') }}: {{ $invoice->due_at->format('d M Y') }}</p>
                @endif
                <p class="text-base">{{ __('invoices.invoice_no')}}: {{ $invoice->number }}</p>
            </div>
            <div class="max-w-[200px] w-full">
                @if ($invoice->status == 'paid')
                    <div class="text-green-500 mt-6 text-lg text-center font-semibold">
                        {{ __('invoices.paid') }}
                    </div>
                @elseif ($invoice->status == 'pending')
                    <div class="text-yellow-500 mb-6 text-lg text-center">
                        {{ __('invoices.payment_pending') }}
                        @if ($checkPayment)
                            <div class="mt-4">
                                <x-button.primary wire:click="checkPaymentStatus" wire:loading.attr="disabled"
                                    class="flex items-center text-sm justify-between" wire:target="checkPaymentStatus">
                                    {{ __('invoices.checking_payment') }}
                                    <x-ri-loader-5-fill aria-hidden="true" class="size-6 me-2 fill-background animate-spin" />
                                    <span class="sr-only">Loading...</span>

                                </x-button.primary>
                            </div>
                        @endif
                    </div>
                    @php
                        $credit = Auth::user()->credits()
                                ->where('currency_code', $invoice->currency_code)
                                ->where('amount', '>', 0)
                                ->first();
                        $itemHasCredit = $invoice->items()->where('reference_type', App\Models\Credit::class)->exists();
                    @endphp
                    @if($credit && !$itemHasCredit)
                        <x-form.checkbox wire:model="use_credits" name="use_credits" :label="__('product.use_credits')" />
                    @endif
                    @if(count($gateways) > 1)
                        <x-form.select wire:model.live="gateway" :label="__('product.payment_method')" class="mt-4" name="gateway">
                            @foreach ($gateways as $gateway)
                                <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                            @endforeach
                        </x-form.select>
                    @endif
                    <x-button.primary wire:click="pay" class="mt-2" wire:loading.attr="disabled" wire:target="pay">
                        <span wire:loading wire:target="pay">Processing...</span>
                        <span wire:loading.remove wire:target="pay">Pay</span>
                    </x-button.primary>
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
                    <div class="text-sm font-medium text-gray-500 uppercase dark:text-base">{{ __('invoices.subtotal') }}</div>
                    <div class="text-base font-medium text-gray-900 dark:text-white">
                        {{ $invoice->formattedTotal->format($invoice->formattedTotal->price - $invoice->formattedTotal->tax) }}
                    </div>
                </div>
                <div class="flex justify-between">
                    <div class="text-sm font-medium text-gray-500 uppercase dark:text-base">
                        {{ \App\Classes\Settings::tax()->name }} ({{ \App\Classes\Settings::tax()->rate }}%)
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
                                <th scope="col"
                                    class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-r-lg">
                                    {{ __('invoices.amount') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->transactions as $transaction)
                                <tr>
                                    <td class="p-4 font-normal whitespace-nowrap">
                                        {{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->transaction_id }}
                                    </td>
                                    <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->gateway?->name }}
                                    </td>
                                    <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->formattedAmount }}
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
