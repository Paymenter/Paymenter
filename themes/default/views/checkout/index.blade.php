<x-app-layout>
    <script>
        function removeElement(element) {
            element.remove();
            this.error = true;
        }
    </script>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <div class="content">
        <x-success />

        @empty(!$products)
            <div class="grid grid-cols-12 gap-4">
                <div class="lg:col-span-8 col-span-12">
                    <div class="content-box !p-0 overflow-hidden">
                        <h2 class="text-xl font-bold px-6 pt-5 pb-2">{{ __('Order overview') }}</h2>
                        <table class="w-full">
                            <thead class="border-b-2 border-secondary-200 dark:border-secondary-50 text-secondary-600">
                                <tr>
                                    <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">
                                        {{ __('Product') }}
                                    </th>
                                    <th scope="col" class="text-start py-2 text-sm font-normal">
                                        {{ __('Quantity') }}
                                    </th>
                                    <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                        {{ __('Price') }}
                                    </th>
                                    <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                        {{ __('Remove') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($products as $product)
                                    @php
                                        ++$i;
                                    @endphp
                                    <tr class="@if(count($products) > $i) border-b-2 border-secondary-200 dark:border-secondary-50 @endif">
                                        <td class="pl-6 py-3">
                                            <div class="flex">
                                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-8 h-8 md:w-12 md:h-12 my-auto rounded-md"
                                                     onerror="removeElement(this);">
                                                <strong class="ml-3 my-auto">{{ ucfirst($product->name) }}</strong>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if ($product->allow_quantity == 1 || $product->allow_quantity == 2)
                                                <form method="POST" action="{{ route('checkout.update', $product->id) }}">
                                                    @csrf
                                                    <input type="number" name="quantity" value="{{ $product->quantity }}"
                                                        class="w-16 border border-secondary-200 dark:border-secondary-50 rounded-md px-2 py-1 text-sm text-center"
                                                        min="1" max="100" />
                                                    <button type="submit" class="button button-secondary-outline">
                                                        <i class="ri-check-line"></i>
                                                    </button>
                                                </form>
                                            @else
                                                {{ $product->quantity }}
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            {{ config('settings::currency_sign') }}
                                            @if ($product->discount)
                                                <span class="text-red-500 line-through">{{ $product->price }}</span>
                                                {{ round($product->price - $product->discount, 2) }}
                                            @else
                                                {{ $product->price }}
                                            @endif
                                            @if($product->quantity >= 1)
                                                {{ __('each') }}
                                            @endif
                                        </td>
                                        <td class="py-3 pr-6">
                                            <form method="POST" action="{{ route('checkout.remove', $product->id) }}">
                                                @csrf
                                                <button type="submit" class="button button-danger-outline">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="lg:col-span-4 col-span-12">
                    <div class="content-box">
                        @if (empty($coupon))
                            <!-- Coupon -->
                            <form method="POST" action="{{ route('checkout.coupon') }}">
                                @csrf
                                <div class="flex flex-row items-center gap-x-2 place-content-stretch">
                                    <x-input type="text" placeholder="{{ __('Coupon') }}" name="coupon" id="password"
                                        icon="ri-coupon-2-line" class="w-full" />
                                    <button type="submit" class="button button-primary">
                                        {{ __('Validate') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="flex items-center justify-between">
                                <div>
                                    {{ __('Coupon:') }}
                                    <span class="text-secondary-900 font-semibold">{{ $coupon->code }}</span>
                                </div>
                                <form method="POST" action="{{ route('checkout.coupon') }}">
                                    @csrf
                                    <input type="text" class="hidden" name="remove" value="1">
                                    <button type="submit" class="button button-danger">
                                        <i class="ri-delete-bin-2-line"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                        <hr class="my-4 border-secondary-300">
                        @foreach ($products as $product)
                            @if ($product->price > 0)
                                @if(count($products) > 1)
                                    <span class="text-sm uppercase font-light text-gray-500 -mb-3">
                                        {{ ucfirst($product->name) }}
                                    </span>
                                @endif
                                <div class="flex flex-row items-center justify-between -mt-1">
                                    <div class="flex flex-row items-center">
                                        <span>
                                            {{ ucfirst($product->billing_cycle) }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span>
                                            @php
                                                if ($product->quantity > 1) {
                                                    $quantity = $product->quantity . " x";
                                                } else {
                                                    $quantity = "";
                                                }
                                            @endphp
                                            @if ($product->discount)
                                                {{ $quantity }} {{ config('settings::currency_sign') }} {{  round($product->price - $product->discount, 2) }}
                                            @else
                                                {{ $quantity }} {{ config('settings::currency_sign') }} {{ $product->price }}
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
                                        <span class="text-lg">
                                            {{ $quantity }} {{ config('settings::currency_sign') }} {{ $product->setup_fee - $product->discount_fee }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if (!empty($discount))
                            <div class="flex flex-row items-center justify-between mt-3">
                                <div class="flex flex-row items-center">
                                    <span>{{ __('Discount') }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span>{{ config('settings::currency_sign') }}
                                        {{ round($discount, 2) }}
                                        @if($coupon->type == "percent")
                                            ({{ $coupon->value }}%)
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="flex flex-row items-center justify-between mt-2">
                            <div class="flex flex-row items-center">
                                <span class="text-lg font-bold">{{ __('Total Today') }}</span>
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
                        <hr class="my-4 border-secondary-300">
                        <form method="POST" action="{{ route('checkout.pay') }}">
                            <div class="flex flex-col">
                                <label for="payment_method"
                                    class="text-sm text-secondary-600">{{ __('Payment method') }}</label>
                                <select id="payment_method" name="payment_method" autocomplete="payment_method"
                                    class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300 border-secondary-300 focus:border-secondary-400 focus:ring-primary-400">
                                    @foreach (App\Models\Extension::where('type', 'gateway')->where('enabled', true)->get() as $gateway)
                                        <option value="{{ $gateway->id }}">
                                            {{ isset($gateway->display_name) ? $gateway->display_name : $gateway->name }}
                                        </option>
                                    @endforeach
                                    @if(config('settings::credits'))
                                        <option value="credits">
                                            {{ __('Pay with credits') }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="items-center p-1">
                                @php
                                    $tos = "I agree to the <a href='" . route('tos') . "' class='text-blue-500 hover:text-blue-600'>terms of service</a>";
                                @endphp
                                <x-input id="tos" type="checkbox" name="tos" required :label="$tos" />
                            </div>
                            @csrf
                            <div class="flex justify-end mt-4">
                                <button type="submit" class="button button-primary">
                                    {{ __('Checkout') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center">
                <p>{{ __('There are no products in your cart') }} </p>
            </div>
        @endempty
    </div>
</x-app-layout>
