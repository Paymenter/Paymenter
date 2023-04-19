<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <x-success class="m-2 mb-4" />
    <div class="grid grid-cols-1 lg:grid-cols-3 m-4">
        @empty(!$products)
            <div class="col-span-1 lg:col-span-2 w-full pr-3">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-darkmode2">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-darkmodetext">
                                {{ __('Product') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-darkmodetext">
                                {{ __('Update') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-darkmodetext">
                                {{ __('Quantity') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-darkmodetext">
                                {{ __('Remove') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 table-auto dark:bg-darkmode2">
                        @foreach ($products as $product)
                            <tr>
                                <td
                                    class="flex flex-row items-center px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-darkmodetext ">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-10 h-10"
                                        onerror="this.classList.add('hidden');">
                                    <div class="flex flex-col ml-4">
                                        <span class="text-lg font-bold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($product->allow_quantity == 1 || $product->allow_quantity == 2)
                                        <form method="POST" action="{{ route('checkout.update', $product->id) }}">
                                            @csrf
                                            <div
                                                class="flex flex-row items-center px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-darkmodetext">
                                                <input type="number" name="quantity" value="{{ $product->quantity }}"
                                                    min="1"
                                                    max="{{ $product->stock_enabled ? $product->stock : '' }}"
                                                    class="w-20 h-10 text-center rounded-md dark:bg-darkmode2 dark:text-darkmodetext"
                                                    min="1">
                                                <button type="submit" class="ml-4">
                                                    <span name="refresh" class="w-6 h-6 text-gray-500 hover:text-gray-700">
                                                        Update
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-lg text-gray-500 whitespace-nowrap dark:text-darkmodetext">
                                    <span class="text-lg font-bold">{{ config('settings::currency_sign') }}
                                        @if ($product->discount)
                                            <span class="text-red-500 line-through">{{ $product->price }}</span>
                                            {{ round($product->price - $product->discount, 2) }}
                                        @else
                                            {{ $product->price }}
                                        @endif
                                        {{ __('each') }}
                                    </span>
                                    <br>
                                    <span class="text-gray-500">{{ __('Quantity:') }} {{ $product->quantity }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap dark:text-darkmodetext">
                                    <form method="POST" action="{{ route('checkout.remove', $product->id) }}">
                                        @csrf
                                        <button type="submit" class="text-xl text-red-500 hover:text-red-600">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pl-3 border-l">
                @if (empty($coupon))
                    <!-- Coupon -->
                    <form method="POST" action="{{ route('checkout.coupon') }}" class="my-4">
                        @csrf
                        <div class="flex flex-row items-center">
                            <input type="text" name="coupon" placeholder="{{ __('Coupon') }}"
                                class="w-full h-10 text-center rounded-md dark:bg-darkmode2 dark:text-darkmodetext">
                            <button type="submit" class="ml-4 form-submit items-center w-40">
                                <span name="refresh" class="w-6 h-6 text-white text-center">
                                    <i class="ri-coupon-2-line"></i>
                                </span>
                                Validate
                            </button>
                        </div>
                    </form>
                @else
                    <div class="flex flex-row items-center relative">
                        <span class="text-lg font-bold">{{ __('Coupon:') }}</span>
                        <span class="text-lg font-bold ml-4">{{ $coupon->code }}</span>
                        <form method="POST" action="{{ route('checkout.coupon') }}"
                            class="ml-4 self-end right-0 absolute">
                            @csrf
                            <input type="text" class="hidden" name="remove" value="1">
                            <button type="submit" class="form-submit items-center w-40 bg-red-500 hover:bg-red-600">
                                <span name="refresh" class="w-6 h-6 text-white text-center">
                                    <i class="ri-delete-bin-2-line"></i>
                                </span>
                                Remove
                            </button>
                        </form>
                    </div>
                @endif
                <hr class="my-4">
                @if (!empty($discount))
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex flex-row items-center">
                            <span class="text-lg font-bold">{{ __('Discount') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg font-bold">{{ config('settings::currency_sign') }}
                                {{ round($discount, 2) }}</span>
                        </div>
                    </div>
                @endif                
                @foreach ($products as $product)
                    @if($product->price > 0)
                    <div class="flex flex-row items-center justify-between">

                        <div class="flex flex-row items-center">
                            <span class="text-lg">
                                {{ ucfirst($product->billing_cycle) }}
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg">{{ config('settings::currency_sign') }}
                                @if ($product->discount)
                                    {{ round($product->price - $product->discount, 2) }}
                                @else
                                    {{ $product->price }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @endif
                    @if ($product->setup_fee > 0)
                        <div class="flex flex-row items-center justify-between">
                            <div class="flex flex-row items-center">
                                <span class="text-lg">
                                    {{ __('Setup fee') }}
                                </span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg">{{ config('settings::currency_sign') }}
                                    {{ $product->setup_fee }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="flex flex-row items-center justify-between">
                    <div class="flex flex-row items-center">
                        <span class="text-lg font-bold">{{ __('Total') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold">{{ config('settings::currency_sign') }}
                            @if (!empty($discount))
                                {{ round($total - $discount, 2) }}
                            @else
                                {{ $total }}
                            @endif
                        </span>
                    </div>
                </div>
                <hr class="my-4">

                <form method="POST" action="{{ route('checkout.pay') }}">
                    <div class="flex flex-col">
                        <label for="payment_method"
                            class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">{{ __('Payment method') }}</label>
                        <select id="payment_method" name="payment_method" autocomplete="payment_method"
                            class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode2 dark:text-darkmodetext">
                            @foreach (App\Models\Extension::where('type', 'gateway')->where('enabled', true)->get() as $gateway)
                                <option value="{{ $gateway->id }}">
                                    {{ isset($gateway->display_name) ? $gateway->display_name : $gateway->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @csrf
                    <div class="flex flex-row justify-end mt-4">
                        <button type="submit" class="form-submit">
                            {{ __('Checkout') }}
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="flex flex-row items-center justify-between">
                <div class="flex flex-row items-center">
                    <span class="text-lg font-bold">{{ __('Your cart is empty') }}</span>
                </div>
                <br>
                <a href="{{ route('products') }}" class="ml-4">
                    <span name="refresh" class="w-6 h-6 text-gray-500 hover:text-gray-700"> {{ __('Go to products') }}
                    </span>
                </a>
            </div>
        @endempty
    </div>
</x-app-layout>
