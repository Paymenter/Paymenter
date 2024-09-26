<div class="grid grid-cols-4 gap-6">
    <div class="flex flex-col col-span-3 gap-4">
        @if ($items->isEmpty())
            <h1 class="text-2xl font-semibold">
                {{ __('product.empty_cart') }}
            </h1>
        @endif
        @foreach ($items as $key => $item)
            <div class="flex flex-row justify-between w-full bg-primary-800 p-2 px-4 rounded-md">
                <div class="flex flex-col gap-1">
                    <h2 class="text-2xl font-semibold">
                        {{ $item->product->name }}
                    </h2>
                    <p class="text-sm">
                        @foreach ($item->configOptions as $option)
                            {{ $option->option_name }}: {{ $option->value_name }}<br>
                        @endforeach
                    </p>
                </div>
                <div class="flex flex-col justify-between items-end gap-4">
                    <h3 class="text-xl font-semibold p-1">
                        {{ $item->price->format($item->price->price * $item->quantity) }} @if ($item->quantity > 1)
                            ({{ $item->price }} each)
                        @endif
                    </h3>
                    <div class="flex flex-row gap-2">
                        @if ($item->product->allow_quantity == 'combined')
                            <div class="flex flex-row gap-1 items-center mr-4">
                                <x-button.secondary
                                    wire:click="updateQuantity({{ $key }}, {{ $item->quantity - 1 }})"
                                    class="h-full !w-fit">
                                    -
                                </x-button.secondary>
                                <x-form.input class="h-10 text-center" disabled
                                    wire:model="items.{{ $key }}.quantity" divClass="!mt-0 !w-14"
                                    name="quantity" />
                                <x-button.secondary
                                    wire:click="updateQuantity({{ $key }}, {{ $item->quantity + 1 }});"
                                    class="h-full !w-fit">
                                    +
                                </x-button.secondary>
                            </div>
                        @endif
                        <a href="{{ route('products.checkout', [$item->product->category, $item->product, 'edit' => $key]) }}"
                            wire:navigate>
                            <x-button.primary class="h-fit w-fit">
                                {{ __('product.edit') }}
                            </x-button.primary>
                        </a>
                        <x-button.secondary wire:click="removeProduct({{ $key }})" class="h-fit !w-fit">
                            {{ __('product.remove') }}
                        </x-button.secondary>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex flex-col gap-2 w-full col-span-1">
        @if (!$items->isEmpty())
            <h2 class="text-2xl font-semibold bg-primary-800 p-2 px-4 rounded-md mb-3">
                {{ __('product.order_summary') }}
            </h2>
            <div class="font-semibold flex items-end gap-2 bg-primary-800 p-2 px-4 rounded-md">
                @if(!$coupon)
                    <x-form.input wire:model="coupon" name="coupon" label="Coupon" />
                    <x-button.primary wire:click="applyCoupon" class="h-fit !w-fit mb-0.5">
                        {{ __('product.apply') }}
                    </x-button.primary>
                @else
                    <div class="flex justify-between items-center w-full">
                        <h4 class="text-center w-full">{{ $coupon->code }}</h4>
                        <x-button.secondary wire:click="removeCoupon" class="h-fit !w-fit">
                            {{ __('product.remove') }}
                        </x-button.secondary>
                    </div>
                @endif
            </div>
            <div class="font-semibold flex justify-between bg-primary-800 p-2 px-4 rounded-md">
                <h4>Subtotal:</h4> {{ $total->format($total->price - $total->tax) }}
            </div>
            @if ($total->tax > 0)
                <div class="font-semibold flex justify-between bg-primary-800 p-2 px-4 rounded-md">
                    <h4>{{ \App\Classes\Settings::tax()->name }}:</h4> {{ $total->formatted->tax }}
                </div>
            @endif
            <div class="text-lg font-semibold flex justify-between bg-primary-800 p-2 px-4 rounded-md mt-1">
                <h4>Total:</h4> {{ $total }}
            </div>

            @if(count($gateways) > 1)
                <x-form.select wire:model.live="gateway" name="gateway" label="Payment Gateway">
                    @foreach ($gateways as $gateway)
                        <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                    @endforeach
                </x-form.select>
            @endif

            <div class="flex flex-row justify-end gap-2">
                <x-button.primary wire:click="checkout" class="h-fit">
                    {{ __('product.checkout') }}
                </x-button.primary>
            </div>
        @endif
    </div>
</div>
