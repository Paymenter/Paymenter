<div class="grid grid-cols-4 gap-6">
    <div class="flex flex-col gap-4 w-full col-span-3">
        <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
        <div class="flex flex-row w-full gap-4">
            @if ($product->image)
                <img src="{{ url()->to($product->image) }}" alt="{{ $product->name }}" class="max-w-40 h-fit">
            @endif
            <article class="prose prose-invert prose-sm">
                {!! $product->description !!}
            </article>
        </div>
        @if ($product->availablePlans()->count() > 1)
            <x-form.select wire:model.live="plan_id" class="text-white bg-primary-800 px-2.5 py-2.5 rounded-md w-full"
                name="plan_id" label="Select a plan">
                @foreach ($product->availablePlans() as $availablePlan)
                    <option value="{{ $availablePlan->id }}">
                        {{ $availablePlan->name }} -
                        {{ $availablePlan->price() }}
                        @if ($availablePlan->price()->has_setup_fee)
                            + {{ $availablePlan->price()->setup_fee }} {{ __('product.setup_fee') }}
                        @endif
                    </option>
                @endforeach
            </x-form.select>
        @endif

        @foreach ($product->configOptions as $configOption)
            <x-form.configoption :config="$configOption" :name="'configOptions.' . $configOption->id">
                @if (in_array($configOption->type, ['select', 'slider']))
                    @foreach ($configOption->children as $configOptionValue)
                        <option value="{{ $configOptionValue->id }}">
                            {{ $configOptionValue->name }}
                            {{ $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available ? ' - ' . $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '' }}
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
                                {{ $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available ? ' - ' . $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '' }}
                            </label>
                        </div>
                    @endforeach
                @endif
            </x-form.configoption>
        @endforeach
    </div>
    <div class="flex flex-col gap-4 w-full col-span-1">
        <h2 class="text-2xl font-semibold">
            {{ __('product.price') }}
        </h2>

        <h3 class="text-xl font-semibold">
            {{ $total }}
        </h3>
        @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
            <div>
                <x-button.primary wire:click="checkout">
                    {{ __('product.checkout') }}
                </x-button.primary>
            </div>
        @endif
    </div>
</div>