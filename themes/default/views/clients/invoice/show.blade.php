<x-app-layout>
    <x-slot name="title">
        {{ __('Invoice') }}
    </x-slot>
    <section class="py-20">
        <div class="max-w-5xl mx-auto py-16 dark:bg-darkmode2 bg-white">
            <article class="overflow-hidden">
                <div class="dark:bg-darkmode2 bg-[white] rounded-b-md">
                    <div class="p-9">
                        <div class="space-y-6 text-slate-700">
                            <x-application-logo />

                            <p class="dark:text-darkmodetext text-xl font-extrabold tracking-tight uppercase font-body">
                                {{ config('app.name', 'Paymenter') }}
                            </p>
                        </div>
                    </div>
                    <div class="p-9">
                        <div class="flex w-full">
                            <div class="grid grid-cols-4 gap-12">
                                <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                    <p class="dark:text-darkmodetext text-sm font-normal text-slate-700">
                                        {{ __('Invoice Detail:') }}
                                    </p>
                                    <p>{{ auth()->user()->name }}</p>
                                    <p>{{ auth()->user()->address }}</p>
                                    <p>{{ auth()->user()->country }}</p>
                                    <p>{{ auth()->user()->zip }}</p>
                                </div>
                                <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                    <p class="dark:text-darkmodetext text-sm font-normal text-slate-700">
                                        {{ __('Billed To') }}</p>
                                    <p>{{ config('app.name', 'Paymenter') }}</p>
                                </div>
                                <div class="dark:text-darkmodetext text-sm font-light text-slate-500">
                                    <p class="dark:text-darkmodetext text-sm font-normal text-slate-700">
                                        {{ __('Invoice Number') }}</p>
                                    <p>{{ $invoice->id }}</p>

                                    <p class="dark:text-darkmodetext mt-2 text-sm font-normal text-slate-700">
                                        {{ __('Date of Issue') }}
                                    </p>
                                    <p>{{ $invoice->created_at }}</p>
                                </div>
                                @if ($invoice->status == 'pending')
                                    <div class="text-sm font-light text-slate-500">
                                        <p class="dark:text-darkmodetext text-sm font-normal text-slate-700">
                                            {{ __('Due') }}</p>
                                        <p class="dark:text-darkmodetext">{{ $order->expiry_date }}</p>
                                        <p class="dark:text-darkmodetext mt-2 text-xl font-normal text-slate-700">
                                            {{ __('Pay') }}
                                        </p>
                                        <p>
                                        <form action="{{ route('clients.invoice.pay', $invoice->id) }}" method="post">
                                            @csrf
                                            <label for="payment_method"
                                                class="dark:text-darkmodetext block text-sm font-medium text-gray-700">{{ __('Payment method') }}</label>
                                            <select id="payment_method" name="payment_method"
                                                autocomplete="payment_method"
                                                class="dark:bg-darkmode dark:text-darkmodetext dark:border-indigo-600 mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                @foreach (App\Models\Extension::where('type', 'gateway')->where('enabled', true)->get() as $gateway)
                                                    <option class="dark:bg-darkmode dark:text-darkmodetext"
                                                        value="{{ $gateway->id }}">
                                                        {{ isset($gateway->display_name) ? $gateway->display_name : $gateway->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('Pay') }}
                                            </button>
                                        </form>
                                        </p>
                                    </div>
                                @else
                                    <div class="text-sm font-light text-slate-500">

                                        <p class="dark:text-darkmodetext text-xl font-normal text-slate-700">
                                            {{ __('Paid') }}
                                        </p>
                                        <p class="dark:text-darkmodetext">{{ $invoice->paid_at }}</p>
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
                                    @foreach($products as $product2)
                                        @php $product = $product2; @endphp
                                        <tr class="border-b border-slate-200">
                                            <td class="py-4 pl-4 pr-3 text-sm sm:pl-6 md:pl-0">
                                                <div class="dark:text-darkmodetext font-medium text-slate-700">
                                                    {{ $product->name ?? $product2->description }}
                                                </div>
                                                <div class="dark:text-darkmodetext mt-0.5 text-slate-500 sm:hidden">
                                                    {{ __('1 unit at') }}
                                                    {{ $currency_sign }}{{ number_format((float) $product->price, 2, '.', '') }}
                                                </div>
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext hidden px-3 py-4 text-sm text-right text-slate-500 sm:table-cell">
                                                {{ $product->quantity ?? $product2->quantity }}
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext hidden px-3 py-4 text-sm text-right text-slate-500 sm:table-cell">
                                                @if ($product->discount)
                                                    <span class="text-red-500 line-through">
                                                        {{ $currency_sign }}{{ number_format((float) $product->price, 2, '.', '') }}
                                                    </span>
                                                    {{ $currency_sign }}{{ number_format((float) ($product->price - $product->discount), 2, '.', '') }}
                                                @else
                                                    {{ $currency_sign }}{{ number_format((float) $product->price, 2, '.', '') }}
                                                @endif
                                            </td>
                                            <td
                                                class="dark:text-darkmodetext py-4 pl-3 pr-4 text-sm text-right text-slate-500 sm:pr-6 md:pr-0">
                                                {{ $currency_sign }}{{ number_format((float) ($product->price * $product->quantity), 2, '.', '') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <!--                                     can be enabled if this is made
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
                                
                                
                                    <tr>
                                        <th scope="row" colspan="3"
                                            class="hidden pt-4 pl-6 pr-3 text-sm font-light text-right text-slate-500 sm:table-cell md:pl-0">
                                            Tax
                                        </th>
                                        <th scope="row"
                                            class="pt-4 pl-4 pr-3 text-sm font-light text-left text-slate-500 sm:hidden">
                                            Tax
                                        </th>
                                        <td class="pt-4 pl-3 pr-4 text-sm text-right text-slate-500 sm:pr-6 md:pr-0">
                                            $0.00
                                        </td>
                                    </tr>-->
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
                                            {{ $currency_sign }}{{ number_format((float) $subtotal, 2, '.', '') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
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
