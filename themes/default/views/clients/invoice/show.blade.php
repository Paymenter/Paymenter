<x-app-layout clients title="{{ __('Invoice') }}">
    <section class="py-20">
        <div class="max-w-5xl mx-auto dark:bg-secondary-100 bg-white rounded-md">
            <article class="overflow-hidden">
                <div class="dark:bg-secondary-100 bg-[white] rounded-md">
                    <div class="p-9 flex flex-row w-full justify-between">
                        <div class="text-slate-700 flex h-full">
                            <div>
                                <x-application-logo />
                            </div>
                            <span class="ml-2 my-auto dark:text-darkmodetext text-xl font-extrabold font-body">
                                {{ config('app.name', 'Paymenter') }}
                            </span>
                        </div>
                        @if ($invoice->status == 'pending')
                            <div class="">
                                <p class="text-red-500 font-semibold mt-2 text-xl">
                                    {{__('Invoice Not Paid')}}
                                </p>
                            </div>
                        @elseif($invoice->status == 'cancelled')
                            <div class="">
                                <p class="text-orange-500 font-semibold mt-2 text-xl">
                                    {{__('Invoice Cancelled')}}
                                </p>
                            </div>
                        @else
                            <div class="text-end">
                                <p class="text-green-500 font-semibold mt-2 text-xl">
                                    {{__('Invoice Paid')}}
                                </p>
                                <span class="block text-sm text-gray-500 dark:text-darkmodetext">
                                    {{ $invoice->paid_at }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-9">
                        <div class="flex w-full">
                            <div class="grid grid-cols-4 gap-12">
                                <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                    <p class="dark:text-darkmodetext text-sm font-bold text-slate-700">
                                        {{ __('Billed To') }}</p>
                                    <p>{{ config('settings::company_name')??config('app.name', 'Paymenter') }}</p>
                                    <p>{{ config('settings::company_address') }}</p>
                                    <p>{{ config('settings::company_zip') }} {{ config('settings::company_city') }}</p>
                                    <p>{{ config('settings::company_country') }}</p>
                                    <p>{{ config('settings::company_vat') ?__('VAT').":":null }} {{ config('settings::company_vat') }}</p>
                                </div>
                                <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                    <p class="dark:text-darkmodetext text-sm font-bold text-slate-700">
                                        {{ __('Purchaser:') }}
                                    </p>
                                    <p>{{ auth()->user()->name }}</p>
                                    <p>{{ auth()->user()->zip }} {{ auth()->user()->city }}</p>
                                    <p>{{ auth()->user()->address }}</p>
                                    <p>{{ auth()->user()->country }}</p>
                                </div>
                                </div>
                                <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                    <p class="dark:text-darkmodetext text-sm font-bold text-slate-700">
                                        {{ __('Invoice Number') }}:</p>
                                    <p>{{ $invoice->id }}/{{ $invoice->created_at->format('m/Y') }}</p>

                                    <p class="dark:text-darkmodetext mt-2 text-sm font-bold text-slate-700">
                                        {{ __('Date of Issue') }}:
                                    </p>
                                    <p>{{ $invoice->created_at }}</p>
                                </div>
                                @if ($invoice->status == 'pending')
                                    <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                        <p class="dark:text-darkmodetext font-bold text-sm text-slate-700">
                                            {{ __('Due Date') }}</p>
                                        <p>{{ $invoice->due_at??"N/A" }}</p>
                                    </div>
                                @elseif($invoice->status == 'cancelled')
                                    <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                        <p class="dark:text-darkmodetext font-bold text-sm text-slate-700">
                                            {{__('Cancellation Date')}}</p>
                                        <p>{{ $invoice->cancelled_at??"N/A" }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-9">
                        <div class="flex flex-col mx-0 mt-8">
                            <table class="min-w-full divide-y divide-slate-500">
                                <thead>
                                    <tr>
                                        <th scope="col"
                                            class="dark:text-darkmodetext py-3.5 pl-4 pr-3 text-left text-sm font-normal text-slate-700 sm:pl-6 md:pl-0">
                                            {{ __('Description') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext hidden py-3.5 px-3 text-right text-sm font-normal text-slate-700 sm:table-cell">
                                            {{ __('Quantity') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext hidden py-3.5 px-3 text-right text-sm font-normal text-slate-700 sm:table-cell">
                                            {{ __('Rate') }}
                                        </th>
                                        <th scope="col"
                                            class="dark:text-darkmodetext py-3.5 pl-3 pr-4 text-right text-sm font-normal text-slate-700 sm:pr-6 md:pr-0">
                                            {{ __('Amount') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $discount = 0.00; @endphp
                                    @foreach ($products as $product)
                                        @php
                                        if ($product->original_price > $product->price) {
                                            $discount += $product->original_price - $product->price;
                                        }
                                        @endphp
                                        <tr class="border-b border-slate-200">
                                            <td class="py-4 pl-4 pr-3 text-sm sm:pl-6 md:pl-0">
                                                <div class="dark:text-darkmodetext font-medium text-slate-700 @if($invoice->status == 'cancelled') line-through @endif">
                                                    {{ $product->name ?? $product2->description }}
                                                </div>
                                                <div class="dark:text-darkmodetext mt-0.5 text-slate-500 sm:hidden @if($invoice->status == 'cancelled') line-through @endif">
                                                    {{ __('1 unit at') }}
                                                    <x-money :amount="number_format((float) $product->basePrice, 2, '.', '')" />
                                                </div>
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext hidden px-3 py-4 text-sm text-right text-slate-500 sm:table-cell @if($invoice->status == 'cancelled') line-through @endif">
                                                {{ $product->quantity ?? $product2->quantity }}
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext hidden px-3 py-4 text-sm text-right text-slate-500 sm:table-cell @if($invoice->status == 'cancelled') line-through @endif">
                                                @if ($product->discount)
                                                    <span class="text-red-500 line-through">
                                                        <x-money :amount="number_format((float) $product->original_price, 2, '.', '')" />
                                                    </span>
                                                    &nbsp;&nbsp;
                                                    <x-money :amount="number_format((float) $product->price, 2, '.', '')" />
                                                @else
                                                    &nbsp;&nbsp;
                                                    <x-money :amount="number_format((float) $product->price / $product->quantity, 2, '.', '')" />
                                                @endif
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext py-4 pl-3 pr-4 text-sm text-right text-slate-500 sm:pr-6 md:pr-0 @if($invoice->status == 'cancelled') line-through @endif">
                                                <x-money :amount="number_format((float) ($product->price * $product->quantity), 2, '.', '')" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @if($discount > 0)
                                    <tr>
                                        <th scope="row" colspan="3"
                                            class="hidden pt-6 pl-6 pr-3 text-sm font-light text-right text-slate-500 sm:table-cell md:pl-0">
                                            {{__('Discount')}}
                                        </th>
                                        <th scope="row"
                                            class="pt-6 pl-4 pr-3 text-sm font-light text-left text-slate-500 sm:hidden">
                                            {{__('Discount')}}
                                        </th>
                                        <td class="pt-6 pl-3 pr-4 text-sm text-right text-slate-500 sm:pr-6 md:pr-0 @if($invoice->status == 'cancelled') line-through @endif">
                                            <x-money :amount="number_format((float) ($discount), 2, '.', '')" />
                                        </td>
                                    </tr>
                                    @endif
                                    <!--
                                    can be enabled if this is made
                                    <tr>
                                        <th scope="row" colspan="3"
                                            class="hidden pt-6 pl-6 pr-3 text-sm font-light text-right text-slate-500 sm:table-cell md:pl-0">
                                            Subtotal
                                        </th>
                                        <th scope="row"
                                            class="pt-6 pl-4 pr-3 text-sm font-light text-left text-slate-500 sm:hidden">
                                            Subtotal
                                        </th>
                                        <td class="pt-6 pl-3 pr-4 text-sm text-right text-slate-500 sm:pr-6 md:pr-0">
                                            @php $subtotal = 0; @endphp
                                            @foreach ($products as $product)
@php $subtotal += $product->price * $product->quantity; @endphp
@endforeach
                                            {{ $currency_sign }}{{ number_format((float) $subtotal, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" colspan="3"
                                            class="hidden pt-6 pl-6 pr-3 text-sm font-light text-right text-slate-500 sm:table-cell md:pl-0">
                                            Discount
                                        </th>
                                        <th scope="row"
                                            class="pt-6 pl-4 pr-3 text-sm font-light text-left text-slate-500 sm:hidden">
                                            Discount
                                        </th>
                                        <td class="pt-6 pl-3 pr-4 text-sm text-right text-slate-500 sm:pr-6 md:pr-0">
                                            $0.00
                                        </td>
                                    </tr>
                                -->

                                    @if(config('settings::tax_enabled') && $tax->amount > 0)
                                        <tr>
                                            <th scope="row" colspan="3"
                                                class="hidden pt-4 pl-6 pr-3 text-sm font-light text-right text-slate-500 sm:table-cell md:pl-0">
                                                {{ $tax->name }}({{ $tax->rate }}%)
                                            </th>
                                            <th scope="row"
                                                class="pt-4 pl-4 pr-3 text-sm font-light text-left text-slate-500 sm:hidden">
                                                {{ $tax->name }}({{ $tax->rate }}%)
                                            </th>
                                            <td class="pt-4 pl-3 pr-4 text-sm text-right text-slate-500 sm:pr-6 md:pr-0">
                                                <x-money :amount="$tax->amount" />
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th scope="row" colspan="3"
                                            class="dark:text-darkmodetext hidden pt-4 pl-6 pr-3 text-sm font-normal text-right text-slate-700 sm:table-cell md:pl-0">
                                            {{ __('Total') }}
                                        </th>
                                        <th scope="row"
                                            class="dark:text-darkmodetext pt-4 pl-4 pr-3 text-sm font-normal text-left text-slate-700 sm:hidden">
                                            {{ __('Total') }}
                                        </th>
                                        <td
                                            class="dark:text-darkmodetext pt-4 pl-3 pr-4 text-sm font-normal text-right text-slate-700 sm:pr-6 md:pr-0">
                                            <x-money :amount="number_format((float) $total, 2, '.', '')" />
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            @if ($invoice->status == 'pending')
                                <div class="flex justify-between mt-3">
                                    <div>

                                    </div>
                                    <div class="text-sm font-light text-slate-500 col-span-2 text-right justify-end">
                                        <p>
                                        <form action="{{ route('clients.invoice.pay', $invoice->id) }}" method="post">
                                            @csrf
                                            <label for="payment_method"
                                                   class="dark:text-darkmodetext block text-sm font-medium text-gray-700">{{ __('Payment method') }}</label>
                                            <x-input id="payment_method" name="payment_method" type="select"
                                                     autocomplete="payment_method">
                                                @if (config('settings::credits'))
                                                    <option value="credits">
                                                        {{__('Pay with credits')}}
                                                    </option>
                                                @endif
                                                @foreach ($gateways as $gateway)
                                                    <option class="dark:bg-darkmode dark:text-darkmodetext"
                                                            value="{{ $gateway->id }}">
                                                        {{ isset($gateway->display_name) ? $gateway->display_name : $gateway->name }}
                                                    </option>
                                                @endforeach
                                            </x-input>
                                            <button type="submit" class="button button-success bg-green-500 mt-3">
                                                {{__('Pay')}}
                                            </button>
                                        </form>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-48 p-9">
                        <div class="border-t pt-9 border-slate-200">
                            <div class="dark:text-darkmodetext text-center text-sm font-light text-slate-700">
                                <p>
                                    {{ __('Thanks for choosing us. We hope you enjoy your purchase.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
</x-app-layout>
