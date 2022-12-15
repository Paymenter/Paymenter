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
        <style>
            .yourInfo {
                display: flex;
                place-items: center;
                padding-left: 20px;
                padding-top: 15px;
            }
            .panel-body {
                padding-bottom: 20px;
            }
        </style>
        <div class="grid grid-cols-1 lg:grid-cols-4">
            <div class="dark:bg-darkmode2 bg-white rounded-md shadow-lg" style="margin-left: 2rem;">
                <div class="yourInfo">
                    <img class="w-8 h-8 rounded-md" style="align-self: center; width: 2rem; height: 2rem;"
                        src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" />
                    <div class="ml-4 text-lg font-semibold leading-7">
                        Invoices
                    </div>
                </div>
                <div class="flex flex-col text-center">
                    @foreach ($invoices as $invoice)
                        <a href='{{ route("clients.invoice.show", $invoice->id) }}' class="text-blue-500 hover:text-blue-700">
                            Invoice ID: {{$invoice->order()->get()->first()->id}}
                        </a>
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
                                    @foreach ($service->products()->get() as $product)
                                        @php
                                            $product = App\Models\Products::where('id', $product->product_id)
                                                ->get()
                                                ->first();
                                        @endphp
                                        @if ($product)
                                            <tr>
                                                <td class="dark:text-white dark:bg-darkmode2 p-3">
                                                    <strong>{{ ucfirst($product->name) }}</strong>
                                                </td>
                                                <td class="text-center dark:text-white dark:bg-darkmode2 p-3" data-order="0.00">
                                                    @if ($product->price == 0)
                                                        {{ __('Free') }}
                                                    @else
                                                        {{ config('settings::currency_sign') }}{{ number_format((float) $product->price . '', 2, '.', '') }}
                                                    @endif
                                                </td>
                                                <td class="text-center dark:text-white dark:bg-darkmode2 p-3">
                                                    {{ date('l jS F Y', strtotime($service->expiry_date)) }}</td>
                                                <td class="text-center dark:text-white dark:bg-darkmode2 p-3">
                                                    <div class="border border-gray-200">
                                                        @if ($service->status === 'paid')
                                                            <span
                                                                class="label status status-active dark:bg-darkmode2 text-green-500">Active</span>
                                                        @elseif($service->status === 'pending')
                                                            <span
                                                                class="label status status-active dark:bg-darkmode2 text-orange-400">Pending</span>
                                                        @elseif($service->status === 'cancelled')
                                                            <span
                                                                class="label status status-active dark:bg-darkmode2 text-red-600">Expired</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                            <td class="dark:text-white dark:bg-darkmode2 p-3">
                                                    <strong>Something went wrong</strong>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                        @elseif (count($services) <= 0)
                            <tr>
                                <td colspan="4" class="dark:text-white dark:bg-darkmode2" style="text-align: center;">No services found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
