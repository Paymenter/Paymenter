<div class="flex flex-col gap-3 animate-in fade-in duration-500">
    @switch($config->type)
        {{-- Select Dropdown --}}
        @case('select')
            <x-form.select name="{{ $name }}" 
                class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                :label="__($config->label ?? $config->name)" 
                :required="$config->required ?? false"
                :selected="config('configs.' . $config->name)" 
                :multiple="$config->multiple ?? false"
                wire:model.live="{{ $name }}" 
                :placeholder="$config->placeholder ?? 'Select an option'">
                {{ $slot }}
            </x-form.select>
        @break

        {{-- Slider / Range --}}
        @case('slider')
            <div x-data="{
                options: @js($config->children->map(fn($child) => [
                    'option' => $child->name, 
                    'value' => $child->id, 
                    'price' => ($showPriceTag && $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) 
                        ? (string)$child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) 
                        : ''
                ])),
                showPriceTag: @js($showPriceTag),
                selectedOption: 0,
                backendOption: $wire.entangle('{{ $name }}').live,
                progressOption: '0%',

                init() {
                    const initialValue = this.$wire.get('{{ $name }}');
                    const foundIndex = this.options.findIndex(plan => plan.value == initialValue);
                    if (foundIndex !== -1) { 
                        this.selectedOption = foundIndex; 
                    }
                    this.updateSliderVisuals();
                    $watch('selectedOption', Alpine.debounce(() => {
                        this.backendOption = this.options[this.selectedOption].value;
                    }, 300));
                },

                updateSliderVisuals() {
                    this.progressOption = `${(this.selectedOption / (this.options.length - 1)) * 100}%`;
                },

                setOptionValue(index) {
                    this.selectedOption = parseInt(index);
                    this.updateSliderVisuals();
                }
            }" class="flex flex-col gap-4 relative py-2">
                
                <div class="flex items-center justify-between">
                    <label for="{{ $name }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                        {{ $config->label ?? $config->name }}
                        @if($config->required ?? false)
                            <span class="text-red-500 ml-0.5">*</span>
                        @endif
                    </label>
                    <span class="text-xs font-black text-primary-600 dark:text-primary-400" x-text="options[selectedOption]?.option"></span>
                </div>

                <div class="relative flex items-center h-10" :style="`--progress:${progressOption};`" wire:ignore>
                    <div class="absolute left-0 right-0 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-primary-500 to-primary-600 shadow-lg transition-all duration-500 ease-out"
                             :style="`width: ${progressOption}`"></div>
                    </div>

                    <input class="absolute inset-0 z-20 appearance-none cursor-pointer w-full bg-transparent 
                        [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:size-5 
                        [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white 
                        [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-primary-500
                        [&::-webkit-slider-thumb]:shadow-lg [&::-webkit-slider-thumb]:cursor-pointer
                        [&::-moz-range-thumb]:size-5 [&::-moz-range-thumb]:rounded-full 
                        [&::-moz-range-thumb]:bg-white [&::-moz-range-thumb]:border-2 
                        [&::-moz-range-thumb]:border-primary-500 [&::-moz-range-thumb]:cursor-pointer" 
                        type="range" min="0" :max="options.length - 1" 
                        x-model="selectedOption" @input="updateSliderVisuals()" name="{{ $name }}" id="{{ $name }}" />
                </div>

                <ul class="flex justify-between px-1 mt-2">
                    @foreach($config->children as $child)
                        <li class="relative flex justify-center">
                            <button @click="setOptionValue({{ $loop->index }})" 
                                type="button"
                                class="flex flex-col items-center group transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-lg px-2 py-1"
                                :class="selectedOption == {{ $loop->index }} ? 'scale-110' : 'opacity-50 hover:opacity-100'">
                                <span class="text-[9px] font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ $child->name }}
                                </span>
                                @if($showPriceTag && $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available)
                                    <span class="text-[8px] font-bold text-primary-600 dark:text-primary-400 mt-1">
                                        {{ $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->formatted->price ?? $child->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit) }}
                                    </span>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @break

        {{-- Text Inputs --}}
        @case('text') 
        @case('password') 
        @case('email') 
        @case('number') 
        @case('color') 
        @case('file')
            <x-form.input name="{{ $name }}" :type="$config->type" 
                class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                :label="__($config->label ?? $config->name)"
                :placeholder="$config->placeholder ?? $config->default ?? ''" 
                :required="$config->required ?? false" 
                wire:model.live="{{ $name }}" />
        @break

        {{-- Textarea --}}
        @case('textarea')
            <x-form.textarea name="{{ $name }}" 
                class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                :label="__($config->label ?? $config->name)"
                :placeholder="$config->placeholder ?? $config->default ?? ''" 
                :required="$config->required ?? false" 
                wire:model.live="{{ $name }}" 
                :rows="$config->rows ?? 4" />
        @break

        {{-- Checkbox --}}
        @case('checkbox')
            <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <x-form.checkbox name="{{ $name }}" type="checkbox" 
                    :label="__($config->label ?? $config->name) . (($showPriceTag && $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? ' (+ ' . $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->formatted->price . ')' : '')"
                    :required="$config->required ?? false" 
                    wire:model.live="{{ $name }}" />
            </div>
        @break

        {{-- Radio Group --}}
        @case('radio')
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400 block">
                    {{ __($config->label ?? $config->name) }}
                    @if($config->required ?? false)
                        <span class="text-red-500 ml-0.5">*</span>
                    @endif
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{ $slot }}
                </div>
            </div>
        @break

        {{-- Switch / Toggle --}}
        @case('switch')
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <label class="text-xs font-bold text-gray-900 dark:text-white">
                        {{ __($config->label ?? $config->name) }}
                        @if($config->required ?? false)
                            <span class="text-red-500 ml-0.5">*</span>
                        @endif
                    </label>
                    @if($showPriceTag && $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available)
                        <p class="text-[10px] font-bold text-primary-600 dark:text-primary-400 mt-1">
                            + {{ $config->children->first()->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->formatted->price }}
                        </p>
                    @endif
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           wire:model.live="{{ $name }}" 
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:bg-primary-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                </label>
            </div>
        @break

        {{-- Default / Unknown Type --}}
        @default
            <div class="p-4 bg-yellow-50 dark:bg-yellow-950/30 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                <p class="text-xs text-yellow-700 dark:text-yellow-400">
                    Unknown config type: <strong>{{ $config->type }}</strong>
                </p>
            </div>
    @endswitch

    {{-- Description / Help Text --}}
    @isset($config->description)
        <div class="mt-1">
            @isset($config->link)
                <a href="{{ $config->link }}" target="_blank" class="inline-flex items-center text-[9px] font-black uppercase tracking-wider text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors group">
                    {{ $config->description }}
                    <x-ri-arrow-right-up-line class="ml-1 size-3 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                </a>
            @else
                <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider leading-relaxed">{{ $config->description }}</p>
            @endisset
        </div>
    @endisset

    {{-- Error Message --}}
    @error($name)
        <p class="text-[10px] font-bold text-red-500 dark:text-red-400 flex items-center gap-1 mt-1">
            <x-ri-error-warning-line class="size-3" />
            {{ $message }}
        </p>
    @enderror
</div>