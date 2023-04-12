<x-admin-layout>
    <x-slot name="title">
        {{ __('Order: ') }} {{ $order->id }}
    </x-slot>
    <div class="grid grid-cols-1 md:grid-cols-2 mt-4">
        <div class="text-2xl dark:text-darkmodetext">
            {{ __('Order: ') }} {{ $order->id }}
        </div>
        <div class="relative inline-block text-left justify-end">
            <button type="button"
                class="dark:hover:bg-darkmode absolute top-0 right-0 dark:text-darkmodetext dark:bg-darkmode2 inline-flex w-max justify-end bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 mr-4"
                id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="moreOptions">
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                    </path>
                </svg>
            </button>
            <div class="absolute hidden w-max origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-[1]"
                role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                id="moreOptions">
                <div class="py-1 grid grid-cols-1" role="none">
                    <button
                        class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-red-900 dark:hover:text-red-300"
                        role="menuitem" tabindex="-1" id="menu-item-0"
                        onclick="document.getElementById('delete').submit()">
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.orders.delete', $order->id) }}" id="delete">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    <div class="mt-6 text-gray-500 dark:text-darkmodetext">
        <div class="flex flex-col">
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Total') }}:</span>
                    <span>{{ config('settings::currency_sign') }}{{ $order->total() }}</span>
                </div>
            </div>
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Created') }}:</span>
                    <span>{{ $order->created_at }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Updated') }}:</span>
                    <span>{{ $order->updated_at }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col mt-4">
            <span class="font-bold">{{ __('Products') }}:</span>
            <div class="flex flex-col">
                @foreach ($order->products as $product)
                    <div class="flex flex-row justify-between border-b">
                        <div class="flex flex-col">
                            <span class="font-bold">{{ $product->product()->get()->first()->name }}</span>
                            <span class="text-sm">
                                <button
                                    class="text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-500"
                                    data-modal-show="changePriceQuantity{{ $product->id }}"
                                    data-modal-target="changePriceQuantity{{ $product->id }}">
                                    {{ __('Change Price/Quantity') }}
                                </button>
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
                                                    <span class="sr-only">Close modal</span>
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
                                                    <div class="flex flex-row justify-end mt-4">
                                                        <button class="form-submit" type="submit">
                                                            {{ __('Change') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold">{{ __('Price') }}:</span>
                            <span>{{ config('settings::currency_sign') }}{{ $product->price ?? $product->product->price }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold">{{ __('Quantity') }}:</span>
                            <span>{{ $product->quantity }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold">{{ __('Status') }}:</span>
                            <span>{{ $product->status }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold">{{ __('Next due date') }}:</span>
                            <span>{{ $product->expiry_date ? $product->expiry_date : 'N/A' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold">{{ __('Link') }}:</span>
                            <span>{{ $product->link ? $product->link : 'N/A' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Buttons to edit/delete/suspend/unsuspend the user -->
    <div class="flex flex-row justify-between mt-4">
        <div class="flex flex-row">
            @if ($order->status !== 'pending')
                <form method="POST" action="{{ route('admin.orders.delete', $order->id) }}">
                    @method('DELETE')
                    @csrf
                    <button class="form-submit bg-red-600">
                        {{ __('Delete') }}
                    </button>
                </form>
            @elseif($order->status == 'pending')
                <form method="POST" action="{{ route('admin.orders.paid', $order->id) }}">
                    @csrf
                    <button class="form-submit bg-green-600">
                        {{ __('Mark as paid') }}
                    </button>
                </form>
            @endif
        </div>
        <div class="flex flex-row">
            @if ($order->status == 'paid')
                <form method="POST" action="{{ route('admin.orders.suspend', $order->id) }}">
                    @csrf
                    <button class="mr-4 form-submit bg-red-600" type="submit">
                        {{ __('Suspend') }}
                    </button>
                </form>
            @elseif($order->status == 'suspended')
                <form method="POST" action="{{ route('admin.orders.unsuspend', $order->id) }}">
                    @csrf
                    <button class="mr-4 form-submit bg-green-600" type="submit">
                        {{ __('Unsuspend') }}
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.orders.create', $order->id) }}">
                    @csrf
                    <button class="mr-4 form-submit bg-green-600" type="submit">
                        {{ __('Create') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Show all invoices -->
    <div class="flex flex-col mt-8">
        <div class="flex flex-row justify-between">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ __('Invoices') }}
            </h3>
        </div>
        <div class="flex flex-col mt-4 dark:text-white">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2">{{ __('ID') }}</th>
                        <th class="px-4 py-2">{{ __('Status') }}</th>
                        <th class="px-4 py-2">{{ __('Created at') }}</th>
                        <th class="px-4 py-2">{{ __('Paid at') }}</th>
                        <th class="px-4 py-2">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->invoices as $invoice)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $invoice->id }}</td>
                            <td class="px-4 py-2">{{ $invoice->status }}</td>
                            <td class="px-4 py-2">{{ $invoice->created_at }}</td>
                            <td class="px-4 py-2">{{ $invoice->paid_at }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>
