<div class="flex flex-col md:grid md:grid-cols-4 gap-4">
    <div class="flex flex-col col-span-3 gap-4">
        @if (Cart::get()->isEmpty())
            <h1 class="text-2xl font-semibold">
                {{ __('product.empty_cart') }}
            </h1>
        @endif
        @foreach (Cart::get() as $key => $item)
            <div class="flex flex-row justify-between w-full bg-background-secondary p-3 rounded-md">
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
                                <x-form.input class="h-10 text-center" disabled divClass="!mt-0 !w-14" value="{{ $item->quantity }}" name="quantity" />
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
                        <x-button.danger wire:click="removeProduct({{ $key }})" class="h-fit !w-fit">
                            {{ __('product.remove') }}
                        </x-button.danger>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex flex-col gap-4">
        @if (!Cart::get()->isEmpty())
            <div class="flex flex-col gap-2 w-full col-span-1 bg-background-secondary p-3 rounded-md">
                <h2 class="text-2xl font-semibold mb-3">
                    {{ __('product.order_summary') }}
                </h2>
                <div class="font-semibold flex items-end gap-2">
                    @if(!$coupon)
                        <x-form.input wire:model="coupon" name="coupon" label="Coupon" />
                        <x-button.primary wire:click="applyCoupon" class="h-fit !w-fit mb-0.5" wire:loading.attr="disabled">
                            <x-loading target="applyCoupon" />
                            <div wire:loading.remove wire:target="applyCoupon">
                                {{ __('product.apply') }}
                            </div>
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
                <div class="font-semibold flex justify-between">
                    <h4>{{ __('invoices.subtotal') }}:</h4> {{ $total->format($total->price - $total->tax) }}
                </div>
                @if ($total->tax > 0)
                    <div class="font-semibold flex justify-between">
                        <h4>{{ \App\Classes\Settings::tax()->name }} ({{ \App\Classes\Settings::tax()->rate }}%):</h4> {{ $total->formatted->tax }}
                    </div>
                @endif
                <div class="text-lg font-semibold flex justify-between mt-1">
                    <h4>{{ __('invoices.total') }}:</h4> {{ $total }}
                </div>
            </div>

            <div class="flex flex-col gap-2 w-full col-span-1 bg-background-secondary p-3 rounded-md">
                @if($total->price > 0)
                @if(count($gateways) > 1)
                <x-form.select wire:model.live="gateway" name="gateway" :label="__('product.payment_method')">
                    @foreach ($gateways as $gateway)
                    <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                    @endforeach
                </x-form.select>
                @endif
                @if(Auth::check() && Auth::user()->credits()->where('currency_code', Cart::get()->first()->price->currency->code)->exists() && Auth::user()->credits()->where('currency_code', Cart::get()->first()->price->currency->code)->first()->amount > 0)
                    <x-form.checkbox wire:model="use_credits" name="use_credits" label="Use Credits" />
                @endif
                @endif
                @if(config('settings.tos'))
                    <x-form.checkbox wire:model="tos" name="tos">
                        {{ __('product.tos') }}
                        <a href="{{ config('settings.tos') }}" target="_blank" class="text-primary hover:text-primary/80">
                            {{ __('product.tos_link') }}
                        </a>
                    </x-form.checkbox>
                @endif

                <div class="flex flex-row justify-end gap-2">
                    <x-button.primary wire:click="checkout" class="h-fit" wire:loading.attr="disabled">
                        <x-loading target="checkout" />
                        <div wire:loading.remove wire:target="checkout">
                            {{ __('product.checkout') }}
                        </div>
                    </x-button.primary>
                </div>
            </div>
        @endif
    </div>
</div>
