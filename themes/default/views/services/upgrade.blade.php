<div class="container mt-14">
    <h1 class="text-2xl font-bold">
        {{ __('services.upgrade_service', ['service' => $service->product->name]) }}
    </h1>

    <h2 class="text-lg font-semibold mt-4">
        @if($step == 1)
            {{ __('services.upgrade_choose_product') }}
        @else
            {{ __('services.upgrade_choose_config') }}
        @endif
    </h2>


    <div class="grid grid-cols-3 gap-6 mt-2">
        <div class="grid md:grid-cols-2 gap-6 col-span-2">
            @if($step == 1)
            {{-- Show current product, we also allow config upgrades so they can use that --}}
            <div>
                <input type="radio" name="upgrade" value="{{ $service->product->id }}" wire:model.live="upgrade"
                    class="hidden peer" id="product-{{ $service->product->id }}">
                <label for="product-{{ $service->product->id }}"
                    class="flex flex-col cursor-pointer bg-background-secondary hover:bg-background-secondary/80 border border-neutral peer-checked:border-secondary p-4 rounded-lg">
                    <div
                        class="rounded-full border border-background rounded-selector inline-flex items-center justify-center gap-2 align-middle bg-primary/60 w-fit px-2 py-0.5">
                        <p class="">{{ __('services.current_plan') }}</p>
                    </div>
                    @if(theme('small_images', false))
                    <div class="flex gap-x-3 items-center">
                        @endif
                        @if ($service->product->image)
                        <img src="{{ Storage::url($service->product->image) }}" alt="{{ $service->product->name }}"
                            class="rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
                        @endif
                        <h2 class="text-xl font-bold">{{ $service->product->name }}</h2>
                        @if(theme('small_images', false))
                    </div>
                    @endif
                    <article class="prose dark:prose-invert">
                        {!! $service->product->description !!}
                    </article>
                    <h3 class="text-lg font-semibold mb-2">
                        @if($service->plan->type == 'recurring')
                        {{ __('services.price_every_period', [
                            'price' => $service->product->price(null, $service->plan->billing_period, $service->plan->billing_unit,
                            $service->currency_code),
                            'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                            'unit' => strtolower(trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period))
                        ]) }}
                        @else 
                        {{ __('services.price_one_time', [
                            'price' => $service->product->price(null, null, null, $service->currency_code),
                        ]) }}
                        @endif

                    </h3>
                </label>
            </div>
            @foreach ($service->productUpgrades() as $product)
            <div>
                <input type="radio" name="upgrade" value="{{ $product->id }}" wire:model.live="upgrade"
                    class="hidden peer" id="product-{{ $product->id }}">
                <label for="product-{{ $product->id }}"
                    class="flex flex-col cursor-pointer bg-background-secondary hover:bg-background-secondary/80 border border-neutral peer-checked:border-secondary p-4 rounded-lg">
                    @if($upgrade == $product->id)
                    <div
                        class="rounded-full border border-background rounded-selector inline-flex items-center justify-center gap-2 align-middle bg-primary w-fit px-2 py-0.5">
                        <p class="">{{ __('services.new_plan') }}</p>
                    </div>
                    @endif
                    @if(theme('small_images', false))
                    <div class="flex gap-x-3 items-center">
                        @endif
                        @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                            class="rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
                        @endif
                        <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                        @if(theme('small_images', false))
                    </div>
                    @endif
                    <article class="prose dark:prose-invert">
                        {!! $product->description !!}
                    </article>
                    <h3 class="text-lg font-semibold mb-2">
                        @if($service->plan->type == 'recurring')
                        {{ __('services.price_every_period', [
                            'price' => $product->price(null, $service->plan->billing_period, $service->plan->billing_unit,
                            $service->currency_code),
                            'period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '',
                            'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)
                        ]) }}
                        @else 
                        {{ __('services.price_one_time', [
                            'price' => $product->price(null, null, null, $service->currency_code),
                        ]) }}
                        @endif
                    </h3>
                </label>
            </div>
            @endforeach
            @else
            <div class="col-span-2 flex flex-col gap-4">
                @foreach ($upgradeProduct->upgradableConfigOptions as $configOption)
                @php
                    $showPriceTag = $configOption->children->filter(fn ($value) => !$value->price(billing_period: $service->plan->billing_period, billing_unit: $service->plan->billing_unit)->is_free)->count() > 0;
                @endphp
                <x-form.configoption :config="$configOption" :name="'configOptions.' . $configOption->id" :showPriceTag="$showPriceTag" :plan="$service->plan">
                    {{-- If the config option is a select, show the options --}}
                    @if ($configOption->type == 'select')
                        @foreach ($configOption->children as $configOptionValue)
                            <option value="{{ $configOptionValue->id }}">
                                {{ $configOptionValue->name }}
                                {{ ($showPriceTag && $configOptionValue->price(billing_period: $service->billing_period, billing_unit: $service->billing_unit)->available) ? ' - ' . $configOptionValue->price(billing_period: $service->billing_period, billing_unit: $service->billing_unit) : '' }}
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
                                    {{ ($showPriceTag && $configOptionValue->price(billing_period: $service->billing_period, billing_unit: $service->billing_unit)->available) ? ' - ' . $configOptionValue->price(billing_period: $service->billing_period, billing_unit: $service->billing_unit) : '' }}
                                </label>
                            </div>
                        @endforeach
                    @endif
                </x-form.configoption>
                @endforeach
            </div>
            @endif
        </div>
        <div class="flex flex-col gap-2 w-full col-span-1 bg-background-secondary p-3 rounded-md h-fit">
            <h4 class="text-lg font-semibold">{{ __('services.upgrade_summary') }}:</h4>

            <div class="flex items-center text-base">
                <span class="mr-2">{{ __('services.current_plan') }}:</span>
                <span class="text-base/50">{{ $service->product->name }}</span>
            </div>
            @if($upgrade != $service->product->id)
            <div class="flex items-center text-base">
                <span class="mr-2">{{ __('services.new_plan') }}:</span>
                <span class="text-base/50">{{ $upgradeProduct ? $upgradeProduct->name : __('general.select_plan') }}</span>
            </div>
            @endif

            {{--  Total today --}}
            <div class="flex items-center text-base">
                <span class="mr-2">{{ __('services.total_today') }}:</span>
                <span class="text-base/50">{{ $this->totalToday() }}</span>
            </div>

            <div class="flex flex-row justify-end gap-2 mt-2">
                <x-button.primary class="h-fit" :wire:click="($upgradeProduct->upgradableConfigOptions()->count() > 0 && $step == 1)? 'nextStep' : 'doUpgrade'">
                    {{-- If the next upgradeProduct supports config upgrades, show those --}}
                    @if($upgradeProduct && $upgradeProduct->upgradableConfigOptions()->count() > 0 && $step == 1)
                        <span>{{ __('services.next_step') }}</span>
                    @else
                        <span>{{ __('services.upgrade') }}</span>
                    @endif
                </x-button.primary>
            </div>
        </div>
    </div>
</div>