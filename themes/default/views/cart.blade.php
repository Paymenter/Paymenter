<div class="container mt-14">
    <div class="flex flex-col md:grid md:grid-cols-4 gap-8">
        <div class="flex flex-col col-span-3 gap-4">
            @if (Cart::items()->count() === 0)
            <h1 class="text-2xl font-semibold">
                {{ __('product.empty_cart') }}
            </h1>
            @endif
            @foreach (Cart::groupedItems() as $group)
            @php
                $parent = $group['parent'];
                $groupTotal = $group['items']->sum(fn ($item) => (float) $item->price->total * $item->quantity);
                $groupPrice = new \App\Classes\Price(['price' => $groupTotal, 'currency' => Cart::get()->currency]);
            @endphp
            @if ($group['items']->count() > 1)
            <div class="flex flex-col w-full bg-background-secondary border border-neutral rounded-lg p-4 sm:p-6" data-cart-group>
                <div class="flex flex-col">
                    @foreach ($group['items'] as $item)
                    @php
                        $itemPrice = $item->price;
                        $summary = $item->domainSummary();
                    @endphp
                    <div class="flex flex-row justify-between gap-4 py-4 {{ $loop->first ? 'pt-0' : 'border-t border-neutral' }}">
                        <div class="flex flex-col gap-1">
                            <h2 class="{{ $loop->first ? 'text-2xl' : 'text-lg' }} font-semibold">
                                {{ $item->product->name }}
                            </h2>
                            @if ($summary)
                            <p class="text-sm text-primary-500">
                                {{ $item->isDomainProduct() ? 'Domain' : 'Included domain' }}: {{ $summary['domain'] }}
                                · {{ $summary['years'] }} {{ $summary['years'] === 1 ? 'year' : 'years' }}
                            </p>
                            @endif
                            @if (!$item->isDomainProduct() && $item->termLabel())
                            <p class="text-sm text-base/70">
                                {{ __('services.billing_cycle') }}: {{ $item->termLabel() }}
                            </p>
                            @endif
                            @if (!empty($item->config_options))
                            <p class="text-sm">
                                @foreach ($item->config_options as $option)
                                {{ $option['option_name'] }}: {{ $option['value_name'] }}<br>
                                @endforeach
                            </p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            <h3 class="text-xl font-semibold">
                                {{ $itemPrice->format($itemPrice->total * $item->quantity) }}
                            </h3>
                            @if ($item->quantity > 1)
                            <p class="text-sm text-base/70">
                                {{ __('product.each', ['price' => $itemPrice]) }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="flex flex-row flex-wrap justify-between items-center gap-4 border-t border-neutral pt-4 mt-2">
                    <div class="text-xl font-semibold flex items-baseline gap-2">
                        <span class="text-sm font-normal text-base/70">{{ __('invoices.total') }}</span>
                        {{ $groupPrice->format($groupTotal) }}
                    </div>
                    <div class="flex flex-row gap-2 items-center ml-auto">
                        @if ($parent->product->allow_quantity == 'combined')
                        <div class="flex flex-row gap-1 items-center mr-2">
                            <x-button.secondary
                                wire:click="updateQuantity({{ $parent->id }}, {{ $parent->quantity - 1 }})"
                                class="!h-8 !w-8 !p-0">
                                -
                            </x-button.secondary>
                            <x-form.input class="!h-8 !w-12 text-center" disabled divClass="!mt-0 !w-12" value="{{ $parent->quantity }}" name="quantity" />
                            <x-button.secondary
                                wire:click="updateQuantity({{ $parent->id }}, {{ $parent->quantity + 1 }});"
                                class="!h-8 !w-8 !p-0">
                                +
                            </x-button.secondary>
                        </div>
                        @endif
                        @if ($parent->product->plans->count() > 1 || $parent->product->configOptions->isNotEmpty() || !empty($parent->checkout_config))
                        <a href="{{ route('products.checkout', [$parent->product->category, $parent->product, 'edit' => $parent->id]) }}"
                            wire:navigate>
                            <x-button.primary class="!w-fit !px-3 !py-1.5 !text-xs">
                                {{ __('product.edit') }}
                            </x-button.primary>
                        </a>
                        @endif
                        <x-button.danger wire:click="removeProduct({{ $parent->id }})" class="!w-fit !px-3 !py-1.5 !text-xs">
                            <x-loading target="removeProduct({{ $parent->id }})" />
                            <div wire:loading.remove wire:target="removeProduct({{ $parent->id }})">
                                {{ __('product.remove') }}
                            </div>
                        </x-button.danger>
                    </div>
                </div>
            </div>
            @else
            <div class="flex flex-row justify-between w-full bg-background-secondary p-3 rounded-md border border-neutral">
                <div class="flex flex-col gap-1">
                    <h2 class="text-2xl font-semibold">
                        {{ $parent->product->name }}
                    </h2>
                    @if ($summary = $parent->domainSummary())
                        <p class="text-sm text-primary-500">
                            {{ $parent->product->domain_mode === 'required' ? 'Included domain' : 'Domain' }}: {{ $summary['domain'] }}
                            · {{ $summary['years'] }} {{ $summary['years'] === 1 ? 'year' : 'years' }}
                        </p>
                    @endif
                    <p class="text-sm">
                        @foreach ($parent->config_options as $option)
                        {{ $option['option_name'] }}: {{ $option['value_name'] }}<br>
                        @endforeach
                    </p>
                </div>
                <div class="flex flex-col justify-between items-end gap-4">
                    <h3 class="text-xl font-semibold p-1">
                        {{ $parent->price->format($parent->price->total * $parent->quantity) }} @if ($parent->quantity > 1)
                        ({{ __('product.each', ['price' => $parent->price]) }})
                        @endif
                    </h3>
                    <div class="flex flex-row gap-2">
                        @if ($parent->product->allow_quantity == 'combined')
                        <div class="flex flex-row gap-1 items-center mr-4">
                            <x-button.secondary
                                wire:click="updateQuantity({{ $parent->id }}, {{ $parent->quantity - 1 }})"
                                class="h-full !w-fit">
                                -
                            </x-button.secondary>
                            <x-form.input class="h-10 text-center" disabled divClass="!mt-0 !w-14" value="{{ $parent->quantity }}" name="quantity" />
                            <x-button.secondary
                                wire:click="updateQuantity({{ $parent->id }}, {{ $parent->quantity + 1 }});"
                                class="h-full !w-fit">
                                +
                            </x-button.secondary>
                        </div>
                        @endif
                        @if (!$parent->parent_id)
                            @if ($parent->product->plans->count() > 1 || $parent->product->configOptions->isNotEmpty() || !empty($parent->checkout_config))
                            <a href="{{ route('products.checkout', [$parent->product->category, $parent->product, 'edit' => $parent->id]) }}"
                                wire:navigate>
                                <x-button.primary class="h-fit w-fit">
                                    {{ __('product.edit') }}
                                </x-button.primary>
                            </a>
                            @endif
                            <x-button.danger wire:click="removeProduct({{ $parent->id }})" class="h-fit !w-fit">
                                <x-loading target="removeProduct({{ $parent->id }})" />
                                <div wire:loading.remove wire:target="removeProduct({{ $parent->id }})">
                                    {{ __('product.remove') }}
                                </div>
                            </x-button.danger>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        <div class="flex flex-col gap-4">
            @if (Cart::items()->count() > 0)
            <div class="flex flex-col gap-2 w-full col-span-1 bg-background-secondary p-3 rounded-md border border-neutral">
                <h2 class="text-2xl font-semibold mb-3">
                    {{ __('product.order_summary') }}
                </h2>
                <div class="font-semibold flex items-end gap-2">
                    @if(!$coupon)
                    <x-form.input wire:model="coupon" name="coupon" :label="__('product.coupon')" />
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
                    <h4>{{ __('invoices.subtotal') }}:</h4> {{ $total->format($total->subtotal) }}
                </div>
                @if ($total->tax > 0)
                <div class="font-semibold flex justify-between">
                    <h4>{{ \App\Classes\Settings::tax()->name }} ({{ \App\Classes\Settings::tax()->rate }}%):</h4> {{ $total->format($total->tax) }}
                </div>
                @endif
                <div class="text-lg font-semibold flex justify-between mt-1">
                    <h4>{{ __('invoices.total') }}:</h4> {{ $total->format($total->total) }}
                </div>

                <div class="flex flex-col gap-2 w-full col-span-1">
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
            </div>
            @endif
        </div>
    </div>
</div>
