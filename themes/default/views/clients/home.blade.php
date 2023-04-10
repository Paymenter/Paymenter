<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <x-success class="mt-4" />
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" style="padding-bottom: 20px;">
            <div class="overflow-hidden bg-white shadow-lg dark:bg-darkmode2 sm:rounded-lg">
                <div class="p-6 border-gray-200 dark:bg-darkmode2">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12" style="display: flex;">
                            <img class="w-8 h-8 rounded-md" style="align-self: center; width: 3rem; height: 3rem;"
                                src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" />
                        </div>
                        <div class="ml-4 text-lg font-semibold leading-7">
                            {{ __('Welcome back') }}, {{ Auth::user()->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-4">
            <div class="dark:bg-darkmode2 bg-white rounded-md shadow-lg" style="margin-left: 2rem;">
                <div class=" flex items-center pl-5 pt-4">
                    <img class="w-8 h-8 rounded-md" style="align-self: center; width: 2rem; height: 2rem;"
                        src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" />
                    <div class="ml-4 text-lg font-semibold leading-7">
                        {{ __('Invoices')}}
                    </div>
                </div>
                <div class="flex flex-col text-center items-center">
                    @foreach ($invoices as $invoice)
                        <a href='{{ route('clients.invoice.show', $invoice->id) }}'
                            class="text-blue-500 hover:text-blue-700">
                            {{ __('Invoice ID')}}: {{ $invoice->id }}
                        </a>
                        <hr class="w-1/2">
                        <br>
                    @endforeach
                </div>
            </div>
            <div class="items-center col-span-1 sm:px-6 lg:px-8 lg:col-span-3">
                <table id="tableServicesList"
                    class="items-center table bg-white shadow-lg dark:text-white dark:bg-darkmode2 sm:rounded-lg w-full p-1">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2 p-2">{{ __('Product/Service') }}
                            </th>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2 p-2">{{ __('Pricing') }}</th>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2 p-2">{{ __('Next Due Date') }}</th>
                            <th class="dark:text-white sorting_asc dark:bg-darkmode2 p-2">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="w-full">
                        @if (count($services) > 0)
                            @foreach ($services as $service)
                                @foreach ($service->products()->get() as $product2)
                                    @php
                                        $product = $product2->product()->get()->first();
                                    @endphp
                                    @if ($product)
                                        <tr>
                                            <td class="dark:text-white dark:bg-darkmode2 p-3">
                                                <strong>{{ ucfirst($product->name) }}</strong>
                                            </td>
                                            <td class="text-center dark:text-white dark:bg-darkmode2 p-3"
                                                data-order="0.00">
                                                {{ $product->price() ? config('settings::currency_sign') . $product->price() : __('Free') }}
                                            </td>
                                            <td class="text-center dark:text-white dark:bg-darkmode2 p-3">
                                                {{ $product2->expiry_date ? date('l jS F Y', strtotime($product2->expiry_date)) : __('Never') }}
                                            <td class="text-center dark:text-white dark:bg-darkmode2 p-3">
                                                <div class="border border-gray-200">
                                                    @if ($product2->status === 'paid')
                                                        <span
                                                            class="label status status-active dark:bg-darkmode2 text-green-500">{{ __('Active') }}</span>
                                                    @elseif($product2->status === 'pending')
                                                        <span
                                                            class="label status status-active dark:bg-darkmode2 text-orange-400">{{ __('Pending') }}</span>
                                                    @elseif($product2->status === 'cancelled')
                                                        <span
                                                            class="label status status-active dark:bg-darkmode2 text-red-600">{{ __('Expired') }}</span>
                                                    @elseif($product2->status === 'suspended')
                                                        <span
                                                            class="label status status-active dark:bg-darkmode2 text-red-600">{{ __('Suspended') }}</span>
                                                    @else
                                                        <span
                                                            class="label status status-active dark:bg-darkmode2 text-red-600">{{ $product2->status }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="dark:text-white dark:bg-darkmode2 p-3">
                                                <strong>{{ __('Something went wrong') }}</strong>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        @elseif (count($services) <= 0)
                            <tr>
                                <td colspan="4" class="dark:text-white dark:bg-darkmode2"
                                    style="text-align: center;">{{ __('No services found.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
