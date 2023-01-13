<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <section class="py-24 bg-white font-poppins dark:bg-gray-700">
        @empty(!$products)

            <div class="px-4 py-6 mx-auto max-w-7xl lg:py-4 md:px-6">
                <div>
                    <h2 class="mb-8 text-4xl font-bold dark:text-gray-400">Your Cart</h2>
                    <div class="p-6 mb-8 border bg-gray-50 dark:bg-gray-800 dark:border-gray-800">
                        <div class="flex-wrap items-center hidden mb-6 -mx-4 md:flex md:mb-8">
                            <div class="w-full px-4 mb-6 md:w-4/6 lg:w-6/12 md:mb-0">
                                <h2 class="font-bold text-gray-500 dark:text-gray-400">Product Name</h2>
                            </div>
                            <div class="hidden px-4 lg:block lg:w-2/12">
                                <h2 class="font-bold text-gray-500 dark:text-gray-400">Price</h2>
                            </div>
                            <div class="w-auto px-4 md:w-1/6 lg:w-2/12 ">
                                <h2 class="font-bold text-gray-500 dark:text-gray-400">Quantity</h2>
                            </div>
                            <div class="w-auto px-4 text-right md:w-1/6 lg:w-2/12 ">
                                <h2 class="font-bold text-gray-500 dark:text-gray-400"> Subtotal</h2>
                            </div>
                        </div>
                        <div class="py-4 mb-8 border-t border-b border-gray-200 dark:border-gray-700">
                            @foreach ($products as $product)
                                <div class="flex flex-wrap items-center mb-6 -mx-4 md:mb-8">
                                    <div class="w-full px-4 mb-6 md:w-4/6 lg:w-6/12 md:mb-0">
                                        <div class="flex flex-wrap items-center -mx-4">
                                            <div class="w-full px-4 mb-3 md:w-1/3">
                                                <div class="w-full h-96 md:h-24 md:w-24">
                                                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                                        class="object-cover w-full h-full">
                                                </div>
                                            </div>
                                            <div class="w-2/3 px-4">
                                                <h2 class="mb-2 text-xl font-bold dark:text-gray-400">{{ $product->name }}
                                                </h2>
                                                <p class="text-gray-500 dark:text-gray-400 ">{{ $product->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden px-4 lg:block lg:w-2/12">
                                        <p class="text-lg font-bold text-blue-500 dark:text-gray-400">
                                            {{ config('settings::currency_sign') }} {{ $product->price }}</p>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Quantity:
                                            {{ $product->quantity }}</span>
                                    </div>
                                    <div class="w-auto px-4 md:w-1/6 lg:w-2/12 ">
                                        <form action="{{ route('checkout.update', $product->id) }}" id="update-form{{ $product->id }}" method="POST"
                                            class="inline-flex items-center px-4 font-semibold text-gray-500 border border-gray-200 rounded-md dark:border-gray-700 ">
                                            @csrf
                                            <button class="py-2 hover:text-gray-700 dark:text-gray-400" onclick="document.getElementById('{{ $product->id }}').value--;document.getElementById('update-form{{ $product->id }}').submit();">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
                                                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z" />
                                                </svg>
                                            </button>
                                            <input type="number"
                                                class="w-12 px-2 py-4 text-center border-0 rounded-md dark:bg-gray-800 bg-gray-50 dark:text-gray-400 md:text-right"
                                                placeholder="1" id="{{ $product->id }}" value="{{ $product->quantity }}" name="quantity">
                                            <button class="py-2 hover:text-gray-700 dark:text-gray-400" onclick="document.getElementById('{{ $product->id }}').value++;document.getElementById('update-form{{ $product->id }}').submit();">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="w-auto px-4 text-right md:w-1/6 lg:w-2/12 ">
                                        <p class="text-lg font-bold text-blue-500 dark:text-gray-400">
                                            {{ config('settings::currency_sign') }}
                                            {{ $product->price * $product->quantity }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-between">
                        <div class="w-full px-4 mb-4 lg:w-1/2">
                            <div class="flex flex-wrap items-center gap-4 hidden">
                                <span class="text-gray-700 dark:text-gray-400">Apply Coupon</span>
                                <input type="text"
                                    class="w-full px-8 py-4 font-normal placeholder-gray-400 border lg:flex-1 dark:border-gray-700 dark:placeholder-gray-500 dark:text-gray-400 dark:bg-gray-800"
                                    placeholder="x304k45" required>
                                <button
                                    class="inline-block w-full px-8 py-4 font-bold text-center text-gray-100 bg-blue-500 rounded-md lg:w-32 hover:bg-blue-600">Apply</button>
                            </div>
                        </div>
                        <div class="w-full px-4 mb-4 lg:w-1/2 ">
                            <div class="p-6 border border-blue-100 dark:bg-gray-900 dark:border-gray-900 bg-gray-50 md:p-8">
                                <h2 class="mb-8 text-3xl font-bold text-gray-700 dark:text-gray-400">Order Summary</h2>
                                <div
                                    class="flex items-center justify-between pb-4 mb-4 border-b border-gray-300 dark:border-gray-700 ">
                                    <span class="text-gray-700 dark:text-gray-400">Subtotal</span>
                                    <span
                                        class="text-xl font-bold text-gray-700 dark:text-gray-400 ">{{ config('settings::currency_sign') }}
                                        {{ $total }}</span>
                                </div>
                                <div class="flex items-center justify-between pb-4 mb-4 ">
                                    <span class="text-gray-700 dark:text-gray-400">Order Total</span>
                                    <span
                                        class="text-xl font-bold text-gray-700 dark:text-gray-400">{{ config('settings::currency_sign') }}{{ $total }}</span>
                                </div>
                                <h2 class="text-lg text-gray-500 dark:text-gray-400">We offer:</h2>
                                <div class="flex items-center gap-2 mb-4 ">
                                    <a href="#">
                                        <img src="https://i.postimg.cc/g22HQhX0/70599-visa-curved-icon.png" alt=""
                                            class="object-cover h-16 w-26">
                                    </a>
                                    <a href="#">
                                        <img src="https://i.postimg.cc/HW38JkkG/38602-mastercard-curved-icon.png"
                                            alt="" class="object-cover h-16 w-26">
                                    </a>
                                    <a href="#">
                                        <img src="https://i.postimg.cc/HL57j0V3/38605-paypal-straight-icon.png"
                                            alt="" class="object-cover h-16 w-26">
                                    </a>
                                </div>
                                    <form method="POST" action="{{ route('checkout.pay') }}" class="flex items-center justify-between ">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full py-4 font-bold text-center text-gray-100 uppercase bg-blue-500 rounded-md hover:bg-blue-600">Checkout</button>
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="container px-6 py-8 mx-auto">
                <div class="flex flex-col items-center justify-center">
                    <h2 class="text-2xl font-bold text-center text-gray-700 dark:text-gray-200">
                        {{ __('Your cart is empty') }}</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('Go to the shop and add some products.') }}</p>
                    <a href="{{ route('index') }}"
                        class="mt-4 text-blue-500 dark:text-blue-400 hover:underline">{{ __('Go to the shop') }}</a>
                </div>
            </div>
            @endif
        </section>
    </x-app-layout>
