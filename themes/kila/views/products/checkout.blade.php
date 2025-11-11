<div class="mb-8 text-center">
    <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ __('Configure') }}</h1>
    <p class="text-muted">{{ __('Configure your desired options and continue to checkout.') }}</p>
</div>

<div class="flex flex-col md:grid md:grid-cols-4 gap-6">
    <div class="flex flex-col gap-6 w-full col-span-3">
        <div class="kila-card bg-background-secondary border border-neutral p-6">
            <h2 class="text-2xl font-bold mb-4">{{ $product->name }}</h2>
        <div class="flex flex-row w-full gap-4">
            @if ($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="max-w-40 h-fit">
            @endif
            <div class="max-h-28 overflow-y-auto w-full">
                <article class="prose dark:prose-invert prose-sm">
                    {!! $product->description !!}
                </article>
            </div>
        </div>
        </div>

        @if ($product->availablePlans()->count() > 1)
        <div class="kila-card bg-background-secondary border border-neutral p-6">
            <h3 class="text-xl font-bold mb-4">{{ __('Choose Billing Cycle') }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($product->availablePlans() as $availablePlan)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="plan_id" wire:model.live="plan_id" value="{{ $availablePlan->id }}" class="peer sr-only" />
                        <div class="kila-card bg-background-secondary border-2 border-neutral peer-checked:border-primary p-4 transition-all">
                            <div class="flex flex-col items-center text-center">
                                <span class="font-bold text-lg mb-1">{{ $availablePlan->name }}</span>
                                <span class="text-2xl font-bold text-primary mb-1">{{ $availablePlan->price() }}</span>
                                @if ($availablePlan->price()->has_setup_fee)
                                    <span class="text-sm text-muted">+ {{ $availablePlan->price()->formatted->setup_fee }} {{ __('product.setup_fee') }}</span>
                                @endif
                                <div class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 border-neutral peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center hidden peer-checked:flex">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        @foreach ($product->configOptions as $configOption)
            @php
                $showPriceTag = $configOption->children->filter(fn ($value) => !$value->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->is_free)->count() > 0;
            @endphp
            <x-form.configoption :config="$configOption" :name="'configOptions.' . $configOption->id" :showPriceTag="$showPriceTag" :plan="$plan">
                @if ($configOption->type == 'select')
                    @foreach ($configOption->children as $configOptionValue)
                        <option value="{{ $configOptionValue->id }}">
                            {{ $configOptionValue->name }}
                            {{ ($showPriceTag && $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? ' - ' . $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '' }}
                        </option>
                    @endforeach
                @elseif($configOption->type == 'radio')
                    @foreach ($configOption->children as $configOptionValue)
                        <div class="flex items-center gap-2">
                            <input type="radio" id="{{ $configOptionValue->id }}" name="{{ $configOption->id }}"
                                wire:model.live="configOptions.{{ $configOption->id }}"
                                value="{{ $configOptionValue->id }}" />
                            <label for="{{ $configOptionValue->id }}">
                                {{ $configOptionValue->name }}
                                {{ ($showPriceTag && $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? ' - ' . $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '' }}
                            </label>
                        </div>
                    @endforeach
                @endif
            </x-form.configoption>
        @endforeach
        @foreach ($this->getCheckoutConfig() as $configOption)
            @php $configOption = (object) $configOption; @endphp
            <x-form.configoption :config="$configOption" :name="'checkoutConfig.' . $configOption->name">
                @if ($configOption->type == 'select')
                    @foreach ($configOption->options as $configOptionValue => $configOptionValueName)
                        <option value="{{ $configOptionValue }}">
                            {{ $configOptionValueName }}
                        </option>
                    @endforeach
                @elseif($configOption->type == 'radio')
                    @foreach ($configOption->options as $configOptionValue => $configOptionValueName)
                        <div class="flex items-center gap-2">
                            <input type="radio" id="{{ $configOptionValue }}" name="{{ $configOption->name }}"
                                wire:model.live="checkoutConfig.{{ $configOption->name }}"
                                value="{{ $configOptionValue }}" />
                            <label for="{{ $configOptionValue }}">
                                {{ $configOptionValueName }}
                            </label>
                        </div>
                    @endforeach
                @endif
            </x-form.configoption>
        @endforeach
    </div>
    <div class="flex flex-col gap-4 w-full col-span-1 h-fit sticky top-20">
        <div class="bg-primary border-2 border-primary rounded-lg overflow-hidden">
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <h2 class="text-xl font-bold text-white">
                        {{ __('product.order_summary') }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="kila-card bg-background-secondary border border-neutral p-6">
            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center pb-4 border-b border-neutral">
                    <span class="text-muted font-medium">{{ __('product.total_today') }}</span>
                    <span class="text-2xl font-bold text-base">{{ $total }}</span>
                </div>

                @if ($total->setup_fee && $plan->type == 'recurring')
                    <div class="flex justify-between items-center pb-4 border-b border-neutral">
                        <span class="text-muted text-sm">{{ __('product.then_after_x', ['time' => $plan->billing_period . ' ' . trans_choice(__('services.billing_cycles.' . $plan->billing_unit), $plan->billing_period)]) }}</span>
                        <span class="font-semibold">{{ $total->format($total->price - $total->setup_fee) }}</span>
                    </div>
                @endif

                @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
                    <div class="mt-2">
                        <x-button.primary wire:click="checkout" wire:loading.attr="disabled" class="w-full btn-kila-success">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            {{ __('product.checkout') }}
                        </x-button.primary>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
