<div class="container mt-14">
    <div @if ($checkPayment) wire:poll.5s="checkPaymentStatus" @endif>
        @if ($this->pay || $showPayModal)
        @include('invoices.partials.payment-modal')
        @endif

        <div class="flex justify-end">
            <div class="max-w-[200px] w-full text-right">
                <span class="cursor-pointer text-base underline" wire:click="downloadPDF">
                    <span wire:loading wire:target="downloadPDF">
                        <x-ri-loader-5-fill class="size-6 animate-spin" />
                    </span>
                    <span wire:loading.remove wire:target="downloadPDF">
                        {{ __('invoices.download_pdf') }}
                    </span>
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
                    @if($checkPayment || $invoice->transactions->where('status', \App\Enums\InvoiceTransactionStatus::Processing)->where('created_at', '>=', now()->subDays(1))->count() > 0)
                    <div class="text-yellow-500 mb-6 text-lg text-center flex items-center justify-center">
                        {{ __('invoices.payment_processing') }}
                        <x-ri-loader-5-fill aria-hidden="true" class="size-6 ms-2 fill-yellow-600 animate-spin" />
                    </div>
                    @else
                    <div class="mb-6 text-lg text-center">
                        @if($invoice->transactions->where('status', \App\Enums\InvoiceTransactionStatus::Processing)->count() > 0)
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
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->transaction_id }}
                                </td>
                                <td class="p-4 font-normal whitespace-nowrap">
                                    @if($transaction->is_credit_transaction)
                                    {{ __('invoices.paid_with_credits') }}
                                    @else
                                    {{ $transaction->gateway?->name }}
                                    @endif
                                </td>
                                <td class="p-4 font-normal whitespace-nowrap">{{ $transaction->formattedAmount }}
                                </td>
                                <td class="p-4 font-normal whitespace-nowrap">
                                    @if($transaction->status == \App\Enums\InvoiceTransactionStatus::Succeeded)
                                    <span class="text-green-600 font-semibold">{{
                                    __('invoices.transaction_statuses.succeeded') }}</span>
                                    @elseif($transaction->status == \App\Enums\InvoiceTransactionStatus::Processing)
                                    <span class="text-yellow-600 font-semibold flex items-center">
                                        {{ __('invoices.transaction_statuses.processing') }}
                                        <x-ri-loader-5-fill aria-hidden="true"
                                            class="size-6 me-2 fill-yellow-600 animate-spin" />
                                    </span>
                                    @elseif($transaction->status == \App\Enums\InvoiceTransactionStatus::Failed)
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
</div>
