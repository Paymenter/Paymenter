<x-app-layout clients title="{{ __('Home') }}">
    <div class="content">
        <x-success class="mt-4" />
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <div class="content-box">
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
            <div class="lg:col-span-3 col-span-12">
                <div class="content-box">
                    <div class=" flex items-center pl-5 pt-4">
                        <img class="w-8 h-8 rounded-md" style="align-self: center; width: 2rem; height: 2rem;"
                            src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" />
                        <div class="ml-4 text-lg font-semibold leading-7">
                            {{ __('Pending Invoices') }}
                        </div>
                    </div>
                    <div class="flex flex-col items-center">
                        @foreach ($invoices as $invoice)
                            <a href='{{ route('clients.invoice.show', $invoice->id) }}'
                                class="text-blue-500 hover:text-blue-700">
                                {{ __('Invoice ID') }}: {{ $invoice->id }}
                            </a>
                            <hr class="w-1/2">
                            <br>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="lg:col-span-9 col-span-12">
                <div class="content-box">
                    <table class="w-full">
                        <thead class="border-b-2 border-secondary-200 dark:border-secondary-50 text-secondary-600">
                            <tr>
                                <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">

                                    {{ __('Product/Service') }}
                                </th>
                                <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                    {{ __('Pricing') }}</th>
                                <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                    {{ __('Next Due Date') }}
                                </th>
                                <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                    {{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($services) > 0)
                                @foreach ($services as $service)
                                    @foreach ($service->products()->get() as $product2)
                                        @php
                                            $product = $product2
                                                ->product()
                                                ->get()
                                                ->first();
                                        @endphp
                                        @if ($product)
                                            <tr class="border-b-2 border-secondary-200 dark:border-secondary-50 cursor-pointer" @if($product2->status === 'cancelled' || $product2->status === 'suspended') style="opacity: 0.5;" @else onclick="window.location='{{ route('clients.active-products.show', $product2->id) }}';" @endif>
                                                <td class="pl-6 py-3">
                                                    <strong>{{ ucfirst($product->name) }}</strong>
                                                </td>
                                                <td class="py-3" data-order="0.00">
                                                    {{ $product2->price !== '0.00' && $product2->price ? config('settings::currency_sign') . $product2->price : __('Free') }}
                                                </td>
                                                <td class="py-3">
                                                    {{ $product2->expiry_date ? date('l jS F Y', strtotime($product2->expiry_date)) : __('Never') }}
                                                <td class="py-3">
                                                    <div class="border border-gray-200 text-center">
                                                        @if ($product2->status === 'paid')
                                                            <span
                                                                class="label status status-active text-green-500">{{ __('Active') }}</span>
                                                        @elseif($product2->status === 'pending')
                                                            <span
                                                                class="label status status-active text-orange-400">{{ __('Pending') }}</span>
                                                        @elseif($product2->status === 'cancelled')
                                                            <span
                                                                class="label status status-active text-red-600">{{ __('Expired') }}</span>
                                                        @elseif($product2->status === 'suspended')
                                                            <span
                                                                class="label status status-active text-red-600">{{ __('Suspended') }}</span>
                                                        @else
                                                            <span
                                                                class="label status status-active text-red-600">{{ $product2->status }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="py-3">
                                                    <strong>{{ __('Something went wrong') }}</strong>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @elseif (count($services) <= 0)
                                <tr>
                                    <td colspan="4" class="dark:text-white dark:bg-secondary-100"
                                        style="text-align: center;">{{ __('No services found.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
