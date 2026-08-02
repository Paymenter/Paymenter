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
