<x-admin-layout>
    <x-slot name="title">
        {{ __('Order: ') }} {{ $order->id }}
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-md dark:shadow-gray-700 dark:bg-darkmode2 sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-darkmode2 sm:px-20">
                    <div class="grid grid-cols-1 md:grid-cols-2 mt-4">
                        <div class="text-2xl dark:text-darkmodetext">
                            {{ __('Order: ') }} {{ $order->id }}
                        </div>
                        <div class="relative inline-block text-left justify-end">
                            <button type="button"
                                class="dark:hover:bg-darkmode absolute top-0 right-0 dark:text-darkmodetext dark:bg-darkmode2 inline-flex w-max justify-end bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 mr-4"
                                id="menu-button" aria-expanded="true" aria-haspopup="true"
                                data-dropdown-toggle="moreOptions">
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
                                    <span>{{ $order->total }}</span>
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
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ __('Status') }}:</span>
                                    <span>{{ $order->status }}</span>
                                </div>
                            </div>
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ __('Next Due Date') }}:</span>
                                    <span>{{ $order->expiry_date }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col mt-4">
                            <span class="font-bold">{{ __('Products') }}:</span>
                            @if ($order->products->count())
                                @foreach ($products as $product)
                                    <span class="flex flex-row justify-between border-b dark:text-darkmodetext">
                                        <span>{{ $product->product()->get()->first()->name }}</span>
                                        <span>{{ $product->quantity }}</span>
                                        @if ($product->link)
                                            <span
                                                class="hover:underline hover:text-blue-600 text-blue-800 dark:text-blue-400"><a
                                                    href="{{ $product->link }}">{{ __('View') }}</a></span>
                                        @endif
                                    </span>
                                @endforeach
                            @else
                                <span>{{ __('No products found.') }}</span>
                            @endif
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
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
