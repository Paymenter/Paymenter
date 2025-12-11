<div class="flex flex-col gap-1">
    @switch($config->type)
        @case('select')
            <x-form.select name="{{ $name }}" :label="__($config->label ?? $config->name)" :required="$config->required ?? false"
                :selected="config('configs.' . $config->name)" :multiple="$config->multiple ?? false"
                wire:model.live="{{ $name }}" :placeholder="$config->placeholder ?? ''">
                {{ $slot }}
            </x-form.select>
        @break

        @case('slider')
            <div x-data="{
                options: @js($config->children->map(fn($child) => ['option' => $child->name, 'value' => $child->id, 'price' => ($showPriceTag && $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? (string)$child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : ''])),
                showPriceTag: @js($showPriceTag),
                selectedOption: 0,
                backendOption: $wire.entangle('{{ $name }}').live,
                progressOption: '0%',
                segmentsWidthOption: '0%',

                init() {
                    const initialValue = this.$wire.get('{{ $name }}');
                    const foundIndex = this.options.findIndex(plan => plan.value == initialValue);
                    if (foundIndex !== -1) {
                        this.selectedOption = foundIndex;
                    }
                    this.updateSliderVisuals();
                    $watch('selectedOption', Alpine.debounce(() => this.backendOption = this.options[this.selectedOption].value, 300))
                },

                updateSliderVisuals() {
                    this.progressOption = `${(this.selectedOption / (this.options.length - 1)) * 100}%`;
                    this.segmentsWidthOption = `${100 / (this.options.length - 1)}%`;
                },

                setOptionValue(index) {
                    this.selectedOption = parseInt(index);
                    this.updateSliderVisuals();
                }
            }" class="flex flex-col gap-1 relative">
                <label for="{{ $name }}"
                    class="mb-1 text-sm text-primary-100">
                    {{ $config->label ?? $config->name }}
                </label>
                <div class="relative flex items-center" :style="`--progress:${progressOption};--segments-width:${segmentsWidthOption}`" wire:ignore>
                    <div class="
                        absolute left-2.5 right-2.5 h-1.5 bg-background-secondary rounded-full overflow-hidden transition-all duration-500 ease-in-out
                        before:absolute before:inset-0 before:bg-primary
                        before:[mask-image:_linear-gradient(to_right,theme(colors.white),theme(colors.white)_var(--progress),transparent_var(--progress))]
                        [&[x-cloak]]:hidden" aria-hidden="true" x-cloak></div>
                    <input class="
                        relative appearance-none cursor-pointer w-full bg-transparent focus:outline-none transition-all duration-500 ease-in-out
                        [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:w-5
                        [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:shadow-none
                        [&::-webkit-slider-thumb]:focus:ring-0 [&::-moz-range-thumb]:h-5 [&::-moz-range-thumb]:w-5
                        [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-base [&::-moz-range-thumb]:border-none
                        [&::-moz-range-thumb]:shadow-none [&::-moz-range-thumb]:focus:ring-0
                    " type="range" min="0" :max="options.length - 1" x-model="selectedOption" @input="setOptionValue(selectedOption)" aria-label="Option Slider" name="{{ $name }}" id="{{ $name }}" />
                </div>
                <!-- Options -->
                <ul class="flex justify-between text-xs font-medium text-light px-2.5">
                    @foreach($config->children as $child)
                        <li class="relative @if($showPriceTag) pb-7 @else pb-2 @endif">
                            <button @click="setOptionValue({{ $loop->index }})" class="absolute flex flex-col items-center -translate-x-1/2">
                                <span class="text-sm font-semibold">
                                    {{ $child->name }}
                                </span>
                                @if($showPriceTag)
                                    <span class="text-sm font-semibold hidden lg:inline">
                                        {{ ($showPriceTag && $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '' }}
                                    </span>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @break

        @case('dynamic_slider')
            @php
                $metadata = $config->metadata ?? [];
                $min = $metadata['min'] ?? 1024;
                $max = $metadata['max'] ?? 65536;
                $step = $metadata['step'] ?? 1024;
                $default = $metadata['default'] ?? $min;
                $unit = $metadata['unit'] ?? 'MB';
                $displayUnit = $metadata['display_unit'] ?? 'GB';
                $displayDivisor = $metadata['display_divisor'] ?? 1024;
                $resourceType = $metadata['resource_type'] ?? 'custom';
                $pricing = $metadata['pricing'] ?? [];
                $pricingModel = $pricing['model'] ?? 'linear';
                $ratePerUnit = $pricing['rate_per_unit'] ?? 0;
                $basePrice = $pricing['base_price'] ?? 0;
                $tiers = $pricing['tiers'] ?? [];
                $includedUnits = $pricing['included_units'] ?? 0;
                $overageRate = $pricing['overage_rate'] ?? 0;
                $billingPeriod = $plan->billing_period ?? 1;
                $billingUnit = $plan->billing_unit ?? 'month';
                $currencySymbol = config('settings.currency_sign', '$');
            @endphp
            <div x-data="{
                value: $wire.entangle('{{ $name }}').live,
                min: {{ $min }},
                max: {{ $max }},
                step: {{ $step }},
                defaultValue: {{ $default }},
                displayDivisor: {{ $displayDivisor }},
                displayUnit: '{{ $displayUnit }}',
                resourceType: '{{ $resourceType }}',
                pricingModel: '{{ $pricingModel }}',
                ratePerUnit: {{ $ratePerUnit }},
                basePrice: {{ $basePrice }},
                tiers: @js($tiers),
                includedUnits: {{ $includedUnits }},
                overageRate: {{ $overageRate }},
                billingPeriod: {{ $billingPeriod }},
                billingUnit: '{{ $billingUnit }}',
                currencySymbol: '{{ $currencySymbol }}',
                progressPercent: '0%',

                init() {
                    if (!this.value || this.value < this.min) {
                        this.value = this.defaultValue;
                    }
                    this.updateProgress();
                    // Watch for value changes and update progress bar (entangled value auto-syncs to Livewire)
                    $watch('value', Alpine.debounce(() => this.updateProgress(), 300));
                },

                updateProgress() {
                    const range = this.max - this.min;
                    const current = this.value - this.min;
                    this.progressPercent = range > 0 ? `${(current / range) * 100}%` : '0%';
                },

                formatDisplay() {
                    if (this.resourceType === 'cpu') {
                        const cores = this.value / 100;
                        return cores + ' ' + (cores === 1 ? 'core' : 'cores');
                    }
                    const displayValue = this.value / this.displayDivisor;
                    const formatted = displayValue === Math.floor(displayValue)
                        ? Math.floor(displayValue)
                        : displayValue.toFixed(1);
                    return formatted + ' ' + this.displayUnit;
                },

                calculatePrice() {
                    let monthlyPrice;

                    switch (this.pricingModel) {
                        case 'tiered':
                            monthlyPrice = this.calculateTieredPrice();
                            break;
                        case 'base_addon':
                            monthlyPrice = this.calculateBaseAddonPrice();
                            break;
                        default:
                            monthlyPrice = this.calculateLinearPrice();
                    }

                    return (monthlyPrice * this.getBillingMultiplier()).toFixed(2);
                },

                calculateLinearPrice() {
                    const displayValue = this.value / this.displayDivisor;
                    return this.basePrice + (displayValue * this.ratePerUnit);
                },

                calculateTieredPrice() {
                    let remainingUnits = this.value / this.displayDivisor;
                    let total = this.basePrice;
                    let previousLimit = 0;

                    for (const tier of this.tiers) {
                        if (remainingUnits <= 0) break;

                        const tierLimit = tier.up_to ? parseFloat(tier.up_to) : Infinity;
                        const tierSize = tierLimit - previousLimit;
                        const unitsInTier = Math.min(remainingUnits, tierSize);

                        total += unitsInTier * (parseFloat(tier.rate) || 0);
                        remainingUnits -= unitsInTier;
                        previousLimit = tierLimit;
                    }

                    return total;
                },

                calculateBaseAddonPrice() {
                    const displayValue = this.value / this.displayDivisor;
                    const overageUnits = Math.max(0, displayValue - this.includedUnits);
                    return this.basePrice + (overageUnits * this.overageRate);
                },

                getBillingMultiplier() {
                    switch (this.billingUnit) {
                        case 'day': return this.billingPeriod / 30;
                        case 'week': return this.billingPeriod / 4;
                        case 'year': return this.billingPeriod * 12;
                        default: return this.billingPeriod;
                    }
                },

                handleInput() {
                    this.updateProgress();
                }
            }" class="flex flex-col gap-1 relative">
                <label for="{{ $name }}" class="mb-1 text-sm text-primary-100">
                    {{ $config->label ?? $config->name }}
                </label>
                <div class="relative flex items-center" :style="`--progress:${progressPercent}`" wire:ignore>
                    <div class="
                        absolute left-2.5 right-2.5 h-1.5 bg-background-secondary rounded-full overflow-hidden transition-all duration-300 ease-out
                        before:absolute before:inset-0 before:bg-primary
                        before:[mask-image:_linear-gradient(to_right,theme(colors.white),theme(colors.white)_var(--progress),transparent_var(--progress))]
                    " aria-hidden="true"></div>
                    <input class="
                        relative appearance-none cursor-pointer w-full bg-transparent focus:outline-none transition-all duration-300 ease-out
                        [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:w-5
                        [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:shadow-none
                        [&::-webkit-slider-thumb]:focus:ring-0 [&::-moz-range-thumb]:h-5 [&::-moz-range-thumb]:w-5
                        [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-base [&::-moz-range-thumb]:border-none
                        [&::-moz-range-thumb]:shadow-none [&::-moz-range-thumb]:focus:ring-0
                    " type="range"
                        :min="min"
                        :max="max"
                        :step="step"
                        x-model="value"
                        @input="handleInput()"
                        name="{{ $name }}"
                        id="{{ $name }}"
                        aria-label="{{ $config->label ?? $config->name }}" />
                </div>
                <!-- Value and Price Display -->
                <div class="flex justify-between items-center mt-2 px-2.5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-primary-100" x-text="formatDisplay()"></span>
                        <span class="text-xs text-primary-500">({{ $min / $displayDivisor }} - {{ $max / $displayDivisor }} {{ $displayUnit }})</span>
                    </div>
                    @if($showPriceTag ?? true)
                        <span class="text-sm font-semibold text-primary">
                            <span x-text="currencySymbol + calculatePrice()"></span>
                            <span class="text-xs text-primary-500">/ {{ $billingPeriod > 1 ? $billingPeriod . ' ' : '' }}{{ $billingUnit }}{{ $billingPeriod > 1 ? 's' : '' }}</span>
                        </span>
                    @endif
                </div>
            </div>
        @break

        @case('text')
        @case('password')

        @case('email')
        @case('number')

        @case('color')
        @case('file')
            <x-form.input name="{{ $name }}" :type="$config->type" :label="__($config->label ?? $config->name)"
                :placeholder="$config->default ?? ''" :required="$config->required ?? false" wire:model.live="{{ $name }}" :placeholder="$config->placeholder ?? ''" />
        @break

        @case('checkbox')
            <x-form.checkbox name="{{ $name }}" type="checkbox" :label="__($config->label ?? $config->name) . (($showPriceTag && $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? ' - ' . $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) : '')"
                :required="$config->required ?? false" wire:model.live="{{ $name }}" />
        @break

        @case('radio')
            <x-form.radio name="{{ $name }}" :label="__($config->label ?? $config->name)" :required="$config->required ?? false" wire:model.live="{{ $name }}">
                {{  $slot }}
            </x-form.radio>
        @break

        @default
    @endswitch
    @isset($config->description)
        @isset($config->link)
            <a href="{{ $config->link }}" class="text-xs text-primary-500 hover:underline hover:text-secondary group">
                {{ $config->description }}
                <x-ri-arrow-right-long-line class="ml-1 size-3 inline-block -rotate-45 group-hover:rotate-0 transition" />
            </a>
        @else
            <p class="text-xs text-primary-500">{{ $config->description }}</p>
        @endisset
    @endisset
</div>
