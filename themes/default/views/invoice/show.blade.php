<div @if ($checkPayment) wire:poll="checkPaymentStatus" @endif>

    @if ($this->pay)
        <x-modal title="Payment for Invoice #{{ $invoice->id }}" open>
            <div class="mt-8">
                {{ $this->pay }}
            </div>
            <x-slot name="closeTrigger">
                <div class="flex gap-4">
                    Amount: {{ $invoice->formattedTotal }}
                    <button wire:confirm="Are you sure?" wire:click="exitPay" @click="open = false"
                        class="text-primary-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>
        </x-modal>
    @endif
    <div class="bg-primary-800 p-12 rounded-lg mt-2">
        <div class="sm:flex justify-between pr-4 pt-4">
            <h1 class="text-2xl font-bold sm:text-3xl">Invoice #{{ $invoice->id }}</h1>
            <div class="mt-4 sm:mt-0 text-right">
                <p>{{ $invoice->user->name }}</p>
                <p class="text-sm">{{ $invoice->user->address }}</p>
                <p class="text-sm">{{ $invoice->user->city }} {{ $invoice->user->zip }}</p>
                <p class="text-sm">{{ $invoice->user->state }} {{ $invoice->user->country }}</p>

                <p class="mt-4 text-gray-400">Invoice Date: {{ $invoice->issued_at->format('d M Y') }}</p>
            </div>
        </div>
        <div class="sm:flex justify-between pr-4 pt-4">
            <div class="mt-6">
                <p class="uppercase font-bold">Bill To</p>
                <address class="text-gray-400 mt-4">
                    <p>{{ config('settings.company_name') }}</p>
                    <p>{{ config('settings.company_address') }}</p>
                    <p>{{ config('settings.company_city') }} {{ config('settings.company_zip') }}</p>
                    <p>{{ config('settings.company_state') }} {{ config('settings.company_country') }}</p>
                </address>
            </div>
            <div class="max-w-[200px] w-full mt-6">
                @if ($invoice->status == 'paid')
                    <div class="text-green-500 mt-6 text-lg text-center font-semibold">
                        Paid
                    </div>
                @elseif ($invoice->status == 'pending')
                    <div class="text-yellow-500 mb-6 text-lg text-center">
                        Payment pending
                        @if ($checkPayment)
                            <div class="mt-4">
                                <x-button.primary wire:click="checkPaymentStatus" wire:loading.attr="disabled"
                                    wire:target="checkPaymentStatus">
                                    <span wire:loading wire:target="checkPaymentStatus">Checking...</span>
                                </x-button.primary>
                            </div>
                        @endif
                    </div>
                    <x-form.select wire:model.live="gateway" label="Payment Gateway" class="mt-4" name="gateway">
                        @foreach ($gateways as $gateway)
                            <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-button.primary wire:click="pay" class="mt-4" wire:loading.attr="disabled" wire:target="pay">
                        <span wire:loading wire:target="pay">Processing...</span>
                        <span wire:loading.remove wire:target="pay">Pay</span>
                    </x-button.primary>
                @endif
            </div>
        </div>

        <div class="mt-12 border-b border-primary-500 overflow-x-auto">
            <table class="w-full text-white">
                <thead class="bg-primary-900 rounded-lg">
                    <tr>
                        <th scope="col"
                            class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-l-lg">
                            Item
                        </th>
                        <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                            Price
                        </th>
                        <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                            Quantity
                        </th>
                        <th scope="col"
                            class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-r-lg">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td class="p-4 font-normal whitespace-nowrap">{{ $item->description }}</td>
                            <td class="p-4 font-normal whitespace-nowrap text-gray-400">{{ $item->formattedPrice }}
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
                    <div class="text-sm font-medium text-gray-500 uppercase dark:text-gray-400">Subtotal</div>
                    <div class="text-base font-medium text-gray-900 dark:text-white">
                        {{ $invoice->formattedTotal->format($invoice->formattedTotal->price - $invoice->formattedTotal->tax) }}
                    </div>
                </div>
                <div class="flex justify-between">
                    <div class="text-sm font-medium text-gray-500 uppercase dark:text-gray-400">
                        {{ \App\Classes\Settings::tax()->name }}
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
                <h2 class="text-2xl font-bold">Transactions</h2>
                <div class="mt-4">
                    <table class="w-full text-white">
                        <thead class="bg-primary-900 rounded-lg">
                            <tr>
                                <th scope="col"
                                    class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-l-lg">
                                    Date
                                </th>
                                <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                                    Transaction ID
                                </th>
                                <th scope="col" class="p-4 text-xs font-semibold tracking-wider text-left uppercase">
                                    Gateway
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-semibold tracking-wider text-left uppercase rounded-r-lg">
                                    Amount
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
