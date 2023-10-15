<x-admin-layout>
    <x-slot name="title">
        {{ __('Order: ') }} {{ $order->id }}
    </x-slot>

    <div class="flex flex-row justify-between w-full">
        <div class="text-2xl leading-5 mt-1 font-bold dark:text-darkmodetext">
            {{ __('Order: ') }} #{{ $order->id }}
        </div>
        @if($order->status !== 'cancelled')
            <form action="{{ route('admin.orders.delete', $order->id) }} " method="POST">
                @method('DELETE')
                @csrf
                <button class="button button-danger float-right flex items-center"><i class="ri-close-circle-line"></i> {{__('Delete Order Unrecoverable')}}</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-4 w-full">
        <div class="text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
            <div class="flex flex-col lg:flex-row gap-x-4 items-baseline">
                <x-input disabled type="text" name="client" :label="__('Client')" name="title" value="{{ $order->user->name }}" class="w-full mt-2 lg:mt-0" icon="ri-user-line" />

                <x-input disabled type="text" name="total" :label="__('Total')" icon="ri-money-dollar-circle-line" class="w-full mt-2 lg:mt-0" value="{{ ucfirst($order->total()) }} {{ config('settings::currency_sign') }}"/>

                <x-input disabled type="text" name="created_at" :label="__('Created At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $order->created_at }}"/>

                <x-input disabled type="text" name="updated_at" :label="__('Updated At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $order->updated_at }}"/>
            </div>
        </div>
    </div>

    <div class="text-gray-500 dark:text-darkmodetext mt-12 w-full">
        <div class="flex flex-col mt-4 overflow-x-auto">
            <h3 class="text-xl leading-5 font-bold dark:text-darkmodetext">
                {{ __('Products') }}
            </h3>
            <style>
                .button {
                    padding: 4px 10px !important;
                }
            </style>
            <table class="mt-4 gap-y-3 min-w-max overflow-x-auto">
                <thead>
                <tr class="border-b-2 border-secondary-200">
                    <th class="font-bold">{{__('ID')}}</th>
                    <th class="font-bold">{{__('Name')}}</th>
                    <th class="font-bold">{{__('Cost')}}</th>
                    <th class="font-bold">{{__('Status')}}</th>
                    <th class="font-bold">{{__('Active to')}}</th>
                    <th class="font-bold">{{__('Link')}}</th>
                    <th class="font-bold p-2">{{__('Actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->products as $product)
                    <tr class="place-items-center">
                        <td class="text-center">
                            {{ $product->product()->get()->first()->id }}
                        </td>
                        <td class="text-center">
                            {{ $product->product()->get()->first()->name }}
                        </td>
                        <td class="text-center">
                            {{ $product->price ?? $product->product->price }} {{ config('settings::currency_sign') }}
                        </td>
                        <td class="text-center">
                            @if($product->status === 'paid')
                                <span class="text-green-500 font-bold">{{ __('Paid') }}</span>
                            @elseif($product->status === 'pending')
                                <span class="text-red-500 font-bold">{{ __('Pending') }}</span>
                            @else
                                <span class="text-yellow-500 font-bold">{{ ucfirst($product->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $product->expiry_date ? $product->expiry_date->format('Y-m-d') : 'N/A' }}
                        </td>
                        <td class="text-center">
                            {{ $product->link ?: 'N/A' }}
                        </td>

                        <td class="flex space-x-2 p-2 justify-center">
                            <!-- Buttons to edit/delete the user -->
                            <div class="my-auto">
                                <a data-tooltip-target="tooltip-animation1" class="button button-primary" href="{{ route('admin.clients.products', ['user' => $order->user, 'orderProduct' => $product->id]) }}" target="_blank">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <div id="tooltip-animation1" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{__('Edit')}}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>

                            <div>
                                <button data-tooltip-target="tooltip-animation2" class="button button-primary" data-modal-show="changePriceQuantity{{ $product->id }}" data-modal-target="changePriceQuantity{{ $product->id }}">
                                    <i class="ri-swap-line"></i>
                                </button>
                                <div id="tooltip-animation2" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{__('Change price/quantity')}}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>

                            @if(!$product->link)
                                <div>
                                    <form method="POST" action="{{ route('admin.orders.create', $order->id) }}">
                                        @csrf
                                        <button class="button button-success" type="submit" data-tooltip-target="tooltip-animation5">
                                            <i class="ri-add-circle-line"></i>
                                        </button>
                                    </form>
                                    <div id="tooltip-animation5" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        {{__('Create server in extension')}}
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>
                            @endif

                            @if ($product->status == 'pending')
                                <div>
                                    <form method="POST" action="{{ route('admin.orders.paid', $order->id) }}">
                                        @csrf
                                        <button class="button button-success" data-tooltip-target="tooltip-animation6">
                                            <i class="ri-refund-2-line"></i>
                                        </button>
                                    </form>
                                    <div id="tooltip-animation6" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        {{__('Mark as paid')}}
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <form action="{{ route('admin.orders.deleteProduct', ['order' => $order->id, 'product' => $product->id]) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="button button-danger" data-tooltip-target="tooltip-animation7">
                                        <i class="ri-delete-bin-2-line"></i>
                                    </button>
                                </form>

                                <div id="tooltip-animation7" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    Delete product from order
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($product))
    <!-- Change quantity / price modal -->
    <div id="changePriceQuantity{{ $product->id }}" tabindex="-1" aria-hidden="true"
         class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] md:h-full">
        <div class="relative w-full h-full max-w-2xl md:h-auto">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div
                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ __('Change Price/Quantity') }}
                    </h3>
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="changePriceQuantity{{ $product->id }}">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor"
                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">{{__('Close')}}</span>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <form method="POST"
                          action="{{ route('admin.orders.changeProduct', ['order' => $order->id, 'product' => $product->id]) }}">
                        @csrf
                        <div class="flex flex-col">
                            <span class="font-bold">{{ __('Price') }}:</span>
                            <input class="form-input" type="text" name="price"
                                   value="{{ $product->price ?? $product->product->price }}"
                                   placeholder="{{ config('settings::currency_sign') }}{{ __('Price') }}" />
                        </div>
                        <div class="flex flex-col mt-4">
                            <span class="font-bold">{{ __('Quantity') }}:</span>
                            <input class="form-input" type="text" name="quantity"
                                   value="{{ $product->quantity }}"
                                   placeholder="{{ __('Quantity') }}" />
                        </div>
                        <div class="flex flex-col mt-4">
                            <span class="font-bold">{{ __('Expiry Date') }}:</span>
                            <input class="form-input" type="date" name="expiry_date"
                                   value="{{ $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '' }}"
                                   placeholder="{{ __('Expiry Date') }}" />
                        </div>
                        <div class="flex flex-row justify-end mt-4">
                            <button class="button button-primary" type="submit">
                                {{ __('Change') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Show all invoices -->
    <div class="flex flex-col mt-12 w-full">
        <div class="flex flex-row justify-between">
            <h3 class="text-xl leading-5 font-bold dark:text-darkmodetext">
                {{ __('Invoices') }}
            </h3>
        </div>
        <div class="flex flex-col mt-4 text-gray-500 dark:text-darkmodetext overflow-x-auto">
            <table class="w-full text-left min-w-max">
                <thead>
                <tr class="border-b-2 border-secondary-200">
                    <th class="px-4 py-2">{{ __('ID') }}</th>
                    <th class="px-4 py-2">{{ __('Status') }}</th>
                    <th class="px-4 py-2">{{ __('Created at') }}</th>
                    <th class="px-4 py-2">{{ __('Paid at') }}</th>
                    <th class="px-4 py-2">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->invoices as $invoice)
                    <tr class="place-items-center">
                        <td class="px-4 py-2">{{ $invoice->id }}</td>
                        <td class="px-4 py-2">
                            @if($invoice->status === 'paid')
                                <span class="text-green-500 font-bold">{{ __('Paid') }}</span>
                            @elseif($invoice->status === 'pending')
                                <span class="text-red-500 font-bold">{{ __('Pending') }}</span>
                            @else
                                <span class="text-yellow-500 font-bold">{{ ucfirst($invoice->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $invoice->created_at }}</td>
                        <td class="px-4 py-2">{{ $invoice->paid_at }}</td>
                        <td class="px-4 py-2">
                            <div class="my-auto">
                                <a data-tooltip-target="tooltip-animation-invoice" class="button button-primary" href="{{ route('admin.invoices.show', $invoice->id) }}">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <div id="tooltip-animation-invoice" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{__('View Invoice')}}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>
