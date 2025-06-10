<div class="flex flex-col gap-1">
    @switch($config->type)
        @case('select')
            <x-form.select name="{{ $name }}" :label="__($config->label ?? $config->name)" :required="$config->required ?? false"
                :selected="config('configs.' . $config->name)" :multiple="$config->multiple ?? false"
                wire:model.live="{{ $name }}" :placeholder="$config->placeholder ?? ''">
                {{ $slot }}
            </x-form.select>
        @break

        @case('text')
        @case('password')

        @case('email')
        @case('number')
        @case('slider')
            <div x-data="{
                ramPlans: @js($config->children->map(fn($child) => ['ram' => $child->name, 'value' => $child->id])),
                selectedRamIndex: 0,
                progressRam: '0%',
                segmentsWidthRam: '0%',

                init() {
                    const initialValue = this.$wire.get('{{ $name }}');
                    const foundIndex = this.ramPlans.findIndex(plan => plan.value == initialValue);
                    if (foundIndex !== -1) {
                        this.selectedRamIndex = foundIndex;
                    }
                    this.updateSliderVisuals();
                },

                updateSliderVisuals() {
                    this.progressRam = `${(this.selectedRamIndex / (this.ramPlans.length - 1)) * 100}%`;
                    this.segmentsWidthRam = `${100 / (this.ramPlans.length - 1)}%`;
                },

                setRamValue(index) {
                    this.selectedRamIndex = parseInt(index);
                    this.updateSliderVisuals();
                    this.$wire.set('{{ $name }}', this.ramPlans[this.selectedRamIndex].value);
                }
            }" class="flex flex-col gap-1">
                <div class="relative flex items-center" :style="`--progress:${progressRam};--segments-width:${segmentsWidthRam}`">
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
                    " type="range" min="0" :max="ramPlans.length - 1" x-model="selectedRamIndex" @input="setRamValue(selectedRamIndex)" aria-label="RAM Slider">
                </div>
                <!-- Options -->
                <div>
                    <ul class="flex justify-between text-xs font-medium text-light px-2.5">
                        <template x-for="(plan, index) in ramPlans" :key="index">
                            <li class="relative">
                                <button @click="setRamValue(index)" class="absolute -translate-x-1/2">
                                    <span class="hidden lg:inline text-sm font-semibold" x-text="`${plan.ram}`"></span>
                                    <span class="inline lg:hidden" x-text="`${plan.ram}`"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        @break

        @case('color')
        @case('file')
            <x-form.input name="{{ $name }}" :type="$config->type" :label="__($config->label ?? $config->name)"
                :placeholder="$config->default ?? ''" :required="$config->required ?? false" wire:model.live="{{ $name }}" :placeholder="$config->placeholder ?? ''" />
        @break

        @case('checkbox')
            <x-form.checkbox name="{{ $name }}" type="checkbox" :label="__($config->label ?? $config->name)"
                :required="$config->required ?? false" :checked="config('configs.' . $config->name) ? true : false" wire:model="{{ $name }}" />
        @break

        @case('radio')
            <x-form.radio name="{{ $name }}" :label="__($config->label ?? $config->name)"
                :selected="config('configs.' . $config->name)" :required="$config->required ?? false" wire:model="{{ $name }}">
                {{ $slot }}
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
