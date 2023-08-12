<x-app-layout clients title="{{ __('Home') }}">
    <script>
        function removeElement(element) {
            element.remove();
            this.error = true;
        }
    </script>
    <div class="content">
        <x-success class="mt-4" />
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <div class="content-box">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12" style="display: flex;">
                            <img class="w-8 h-8 rounded-md" style="align-self: center; width: 3rem; height: 3rem;"
                                src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"  alt="{{Auth::user()->name}}"/>
                        </div>
                        <div class="ml-4 text-lg font-semibold leading-7">
                            {{ __('Welcome back') }}, {{ Auth::user()->name }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-3 col-span-12 flex-col">
                <div class="bg-secondary-100 rounded-xl">
                    <div class="flex items-center border-b-2 p-3.5 border-gray-700">
                        <h1 class="text-lg font-semibold mx-auto text-center">
                            {{ __('Pending Invoices') }}
                        </h1>
                    </div>
                    <div class="flex flex-col items-center">
                        @if (count($invoices) === 0)
                            <div class="text-center">
                                <p class="text-blue-500 font-bold text-lg mt-2">
                                    {{__('Hurray! No invoices to pay')}}
                                </p>
                            </div>
                        @endif
                            @foreach ($invoices as $invoice)
                                <div class="hover:bg-secondary-300 transition-all border-b-[1px] border-secondary-200 ease-out hover:ease-in cursor-pointer w-full p-3" onclick="window.location.href='{{ route('clients.invoice.show', $invoice->id) }}'">
                                    <div class="flex flex-row">
                                        <div class="flex my-auto text-2xl ms-2 w-1/12 justify-center text-primary-400">
                                            <i class="ri-bill-fill"></i>
                                        </div>
                                        <div class="w-8/12 ml-4 truncate">
                                            <span class="font-semibold">{{ __('Invoice ID') }}:</span>
                                            <span class="font-semibold">{{ $invoice->id }}</span>
                                            <div class="w-full text-sm text-gray-400 truncate">
                                                <span class="font-semibold">{{__('Amount to pay')}} - {{ $invoice->total() }} {{ config('settings::currency_sign') }}</span>
                                            </div>
                                        </div>
                                        <div class="justify-end flex text-center text-primary-400 my-auto button button-primary text-md w-fit py-[5px] px-[8px]">
                                            <b>{{__('View')}}</b>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-9 col-span-12">
                <div class="content-box">
                    <table class="w-full">
                        <thead class="border-b-2 border-secondary-200 dark:border-secondary-200 text-secondary-600">
                            <tr>
                                <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">
                                    {{ __('Product/Service') }}</th>
                                <th scope="col" class="text-start pr-6 py-2 text-sm font-normal hidden md:table-cell">
                                    {{ __('Cost') }}</th>
                                <th scope="col" class="text-start pr-6 py-2 text-sm font-normal hidden md:table-cell">
                                    {{ __('Active until') }}</th>
                                <th scope="col" class="text-start pr-6 py-2 text-sm font-normal hidden md:table-cell">
                                    {{ __('Status') }}</th>
                                <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                    {{ __('Actions') }}</th>
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
                                    @if($product2->status === 'cancelled')
                                        @php
                                            $product = NULL;
                                        @endphp
                                    @endif
                                    @if ($product)
                                        <tr class="border-b-2 border-secondary-200 dark:border-secondary-200">
                                            <td class="pl-6 py-3 items-center break-all max-w-fit">
                                                <div class="flex">
                                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-8 h-8 md:w-12 md:h-12 my-auto"
                                                         onerror="removeElement(this);">
                                                    <strong class="ml-3 my-auto">{{ ucfirst($product->name) }}</strong>
                                                </div>
                                            </td>
                                            <td class="py-3 hidden md:table-cell" data-order="0.00">
                                                {{ $product2->price !== '0.00' && $product2->price ? $product2->price . " " . config('settings::currency_sign') : __('Free') }}
                                            </td>
                                            <td class="py-3 hidden md:table-cell">
                                                {{ $product2->expiry_date ? date('d-m-Y', strtotime($product2->expiry_date)) : __('Never') }}
                                            </td>
                                            <td class="py-3 hidden md:table-cell">
                                                <div class="font-bold rounded-md text-left">
                                                    @if ($product2->status === 'paid')
                                                        <span
                                                            class="label status status-active text-green-500">{{__('Active')}}</span>
                                                    @elseif($product2->status === 'pending')
                                                        <span
                                                            class="label status status-active text-orange-500">{{ __('Pending') }}</span>
                                                    @elseif($product2->status === 'cancelled')
                                                        <span
                                                            class="label status status-active text-red-500">{{ __('Expired') }}</span>
                                                    @elseif($product2->status === 'suspended')
                                                        <span
                                                            class="label status status-active text-red-500">{{ __('Suspended') }}</span>
                                                    @else
                                                        <span
                                                            class="label status status-active text-red-500">{{ $product2->status }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 flex">
                                                <a class="button button-secondary" @if($product2->status === 'cancelled' || $product2->status === 'suspended') style="opacity: 0.5; cursor: not-allowed;" @else href="{{ route('clients.active-products.show', $product2->id) }}" @endif><i class="ri-eye-line"></i></a>
                                                <span class="relative flex ml-2">
                                                        <a class="button @if($product2->status !== 'pending' || $product2->status !== 'suspended') cursor-pointer bg-secondary-200 hidden @else button-secondary @endif" @if($product2->status === 'pending' || $product2->status === 'suspended') href='{{ route('clients.invoice.index') }}'@endif><i class="ri-money-dollar-circle-line"></i></a>
                                                        @if($product2->status === 'pending' || $product2->status === 'suspended')
                                                        <span class="animate-ping -top-1 -right-1 absolute inline-flex h-3 w-3 rounded-full bg-red-400 opacity-75"></span>
                                                        <span class="absolute inline-flex -top-1 -right-1 rounded-full h-3 w-3 bg-red-500"></span>
                                                    @endif
                                                    </span>

                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        @elseif (count($services) <= 0)
                            <tr>

                            </tr>
                            <tr class="w-full">
                                <td colspan="4" class="w-full dark:text-blue-500 font-bold text-lg text-center dark:bg-secondary-100">
                                    {{ __('No services found.') }}
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
