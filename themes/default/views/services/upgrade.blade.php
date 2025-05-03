<div class="grid grid-cols-3 gap-6">
    <div class="grid md:grid-cols-2 gap-4 col-span-2">
        @foreach ($service->productUpgrades() as $product)
        <div>
            <input type="radio" name="upgrade" value="{{ $product->id }}" wire:model.live="upgrade" class="hidden peer"
                id="product-{{ $product->id }}">
            <label for="product-{{ $product->id }}"
                class="cursor-pointer flex flex-col bg-primary-700 p-4 rounded-md mb-4 border border-neutral peer-checked:border-secondary">
                @if ($product->image)
                <img src="{{ url()->to($product->image) }}" alt="{{ $product->name }}"
                    class="w-full object-cover object-center rounded-md">
                @endif
                <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                <article class="prose dark:prose-invert">
                    {!! $product->description !!}
                </article>
                <h3 class="text-lg font-semibold text-primary-300">
                    {{ $product->price(null, $service->plan->billing_period, $service->plan->billing_unit, $service->order->currency_code) }} every {{
                    $service->plan->billing_period > 1 ? $service->plan->billing_period : '' }}
                    {{ Str::plural($service->plan->billing_unit, $service->plan->billing_period) }}
                </h3>
            </label>
        </div>
        @endforeach
    </div>
    <div class="flex flex-col gap-2 w-full col-span-1">
        <h2 class="text-xl font-semibold bg-primary-800 p-2 px-4 rounded-md mb-3">
            {{ __('services.upgrade_summary') }}
        </h2>
        <div class="font-semibold flex justify-between bg-primary-700 p-2 px-4 rounded-md">
            <h4>{{ __('services.current_plan') }}:</h4>
            <span>{{ $service->product->name }}</span>
        </div>
        <div class="font-semibold flex justify-between bg-primary-700 p-2 px-4 rounded-md">
            <h4>{{ __('services.current_price') }}:</h4>
            <span>{{ $service->formattedPrice }}</span>
        </div>

        @if($upgradeProduct)
        <div class="font-semibold flex justify-between bg-primary-700 p-2 px-4 rounded-md">
            <h4>{{ __('services.new_plan') }}:</h4>
            <span>{{ $upgradeProduct->name }}</span>
        </div>
        <div class="font-semibold flex justify-between bg-primary-700 p-2 px-4 rounded-md">
            <h4>{{ __('services.new_price') }}:</h4>
            <span>{{ $upgradeProduct->price(null, $service->plan->billing_period, $service->plan->billing_unit,
                $service->order->currency_code) }}</span>
        </div>
        @endif

        <div class="text-lg font-semibold flex justify-between bg-primary-700 p-2 px-4 rounded-md mt-1">
            <h4>{{ __('services.total_today') }}:</h4>
            <span>{{ $this->totalToday() }}</span>
        </div>

        <div class="flex flex-row justify-end gap-2 mt-2">
            <x-button.primary wire:click="doUpgrade" class="h-fit">
                {{ __('services.upgrade') }}
            </x-button.primary>
        </div>

    </div>
</div>