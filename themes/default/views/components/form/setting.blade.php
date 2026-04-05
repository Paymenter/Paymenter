<div class="flex flex-col gap-4 animate-in fade-in duration-500">
    @switch($setting->type)
        {{-- Select Dropdown --}}
        @case('select')
            <div class="space-y-2">
                <x-form.select 
                    name="fields.{{ $key }}.{{ $setting->name }}" 
                    class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                    :label="__($setting->label ?? $setting->name)" 
                    :required="$setting->required ?? false"
                    :options="$setting->options ?? []" 
                    :selected="config('settings.' . $setting->name)" 
                    :multiple="$setting->multiple ?? false"
                    wire:model="fields.{{ $key }}.{{ $setting->name }}"
                    :placeholder="'Select ' . __($setting->label ?? $setting->name)"
                />
            </div>
        @break

        {{-- Text Inputs --}}
        @case('text') 
        @case('password') 
        @case('email') 
        @case('number') 
        @case('color') 
        @case('file')
            <div class="space-y-2">
                <x-form.input 
                    name="fields.{{ $key }}.{{ $setting->name }}" 
                    :type="$setting->type" 
                    class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                    :label="__($setting->label ?? $setting->name)"
                    :placeholder="$setting->placeholder ?? $setting->default ?? ''" 
                    :required="$setting->required ?? false" 
                    wire:model="fields.{{ $key }}.{{ $setting->name }}"
                    :value="config('settings.' . $setting->name)"
                    :icon="$setting->icon ?? null"
                />
                
                @if($setting->type === 'file' && config('settings.' . $setting->name))
                    <div class="flex items-center gap-2 mt-2 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                        <x-ri-file-line class="size-4 text-gray-500" />
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ basename(config('settings.' . $setting->name)) }}</span>
                        <button type="button" wire:click="clearFile('{{ $setting->name }}')" class="ml-auto text-red-500 hover:text-red-600 transition-colors">
                            <x-ri-delete-bin-line class="size-4" />
                        </button>
                    </div>
                @endif
            </div>
        @break

        {{-- Textarea --}}
        @case('textarea')
            <div class="space-y-2">
                <x-form.textarea 
                    name="fields.{{ $key }}.{{ $setting->name }}" 
                    class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                    :label="__($setting->label ?? $setting->name)"
                    :placeholder="$setting->placeholder ?? $setting->default ?? ''" 
                    :required="$setting->required ?? false" 
                    wire:model="fields.{{ $key }}.{{ $setting->name }}"
                    rows="4"
                >
                    {{ config('settings.' . $setting->name) ?? '' }}
                </x-form.textarea>
            </div>
        @break

        {{-- Checkbox --}}
        @case('checkbox')
            <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <x-form.checkbox 
                    name="fields.{{ $key }}.{{ $setting->name }}" 
                    type="checkbox" 
                    :label="__($setting->label ?? $setting->name)"
                    :required="$setting->required ?? false" 
                    :checked="config('settings.' . $setting->name) ? true : false" 
                    wire:model="fields.{{ $key }}.{{ $setting->name }}"
                />
            </div>
        @break

        {{-- Switch / Toggle --}}
        @case('switch')
        @case('toggle')
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/30 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <label class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __($setting->label ?? $setting->name) }}
                        @if($setting->required ?? false)
                            <span class="text-red-500 ml-0.5">*</span>
                        @endif
                    </label>
                    @if(isset($setting->description))
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">{{ $setting->description }}</p>
                    @endif
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           wire:model="fields.{{ $key }}.{{ $setting->name }}"
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full peer peer-checked:bg-primary-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                </label>
            </div>
        @break

        {{-- Radio Group --}}
        @case('radio')
            <div class="space-y-3">
                <label class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __($setting->label ?? $setting->name) }}
                    @if($setting->required ?? false)
                        <span class="text-red-500 ml-0.5">*</span>
                    @endif
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($setting->options as $optionValue => $optionLabel)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer transition-all duration-200 hover:border-primary-300 dark:hover:border-primary-700 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/10 dark:has-[:checked]:bg-primary-950/20">
                            <div class="relative">
                                <input type="radio" 
                                       name="fields.{{ $key }}.{{ $setting->name }}"
                                       value="{{ $optionValue }}"
                                       wire:model="fields.{{ $key }}.{{ $setting->name }}"
                                       @if($setting->required ?? false) required @endif
                                       class="peer appearance-none size-4 border-2 border-gray-300 dark:border-gray-600 rounded-full checked:border-primary-500 transition-all duration-200 cursor-pointer">
                                <div class="absolute size-2 bg-primary-500 rounded-full opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 left-0.5 top-0.5 transition-all duration-200 pointer-events-none"></div>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $optionLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @break

        {{-- Range / Slider --}}
        @case('range')
        @case('slider')
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __($setting->label ?? $setting->name) }}
                        @if($setting->required ?? false)
                            <span class="text-red-500 ml-0.5">*</span>
                        @endif
                    </label>
                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                        {{ config('settings.' . $setting->name, $setting->default ?? 0) }}
                    </span>
                </div>
                <input type="range"
                       name="fields.{{ $key }}.{{ $setting->name }}"
                       wire:model="fields.{{ $key }}.{{ $setting->name }}"
                       min="{{ $setting->min ?? 0 }}"
                       max="{{ $setting->max ?? 100 }}"
                       step="{{ $setting->step ?? 1 }}"
                       class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-primary-500">
                <div class="flex justify-between text-[10px] text-gray-500 dark:text-gray-400">
                    <span>{{ $setting->min ?? 0 }}</span>
                    <span>{{ $setting->max ?? 100 }}</span>
                </div>
            </div>
        @break

        {{-- Image Upload --}}
        @case('image')
            <div class="space-y-3">
                <label class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __($setting->label ?? $setting->name) }}
                    @if($setting->required ?? false)
                        <span class="text-red-500 ml-0.5">*</span>
                    @endif
                </label>
                
                <div class="flex items-center gap-4">
                    @if(config('settings.' . $setting->name))
                        <div class="relative group">
                            <img src="{{ Storage::url(config('settings.' . $setting->name)) }}" 
                                 alt="{{ $setting->name }}"
                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            <button type="button" 
                                    wire:click="clearImage('{{ $setting->name }}')"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <x-ri-close-line class="size-3" />
                            </button>
                        </div>
                    @endif
                    
                    <x-form.input 
                        type="file"
                        name="fields.{{ $key }}.{{ $setting->name }}" 
                        class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl"
                        :label="null"
                        wire:model="fields.{{ $key }}.{{ $setting->name }}"
                        accept="image/*"
                    />
                </div>
            </div>
        @break

        {{-- Time --}}
        @case('time')
            <x-form.input 
                type="time"
                name="fields.{{ $key }}.{{ $setting->name }}" 
                class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                :label="__($setting->label ?? $setting->name)"
                :required="$setting->required ?? false" 
                wire:model="fields.{{ $key }}.{{ $setting->name }}"
                :value="config('settings.' . $setting->name)"
            />
        @break

        {{-- Date --}}
        @case('date')
            <x-form.input 
                type="date"
                name="fields.{{ $key }}.{{ $setting->name }}" 
                class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                :label="__($setting->label ?? $setting->name)"
                :required="$setting->required ?? false" 
                wire:model="fields.{{ $key }}.{{ $setting->name }}"
                :value="config('settings.' . $setting->name)"
            />
        @break

        {{-- Datetime Local --}}
        @case('datetime-local')
            <x-form.input 
                type="datetime-local"
                name="fields.{{ $key }}.{{ $setting->name }}" 
                class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                :label="__($setting->label ?? $setting->name)"
                :required="$setting->required ?? false" 
                wire:model="fields.{{ $key }}.{{ $setting->name }}"
                :value="config('settings.' . $setting->name)"
            />
        @break

        {{-- Default / Unknown Type --}}
        @default
            <div class="p-4 bg-yellow-50 dark:bg-yellow-950/30 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                <p class="text-xs text-yellow-700 dark:text-yellow-400">
                    Unknown setting type: <strong>{{ $setting->type }}</strong> for "{{ $setting->name }}"
                </p>
            </div>
    @endswitch

    {{-- Description / Help Text --}}
    @isset($setting->description)
        <div class="mt-1">
            @isset($setting->link)
                <a href="{{ $setting->link }}" target="_blank" class="inline-flex items-center text-[9px] font-black uppercase tracking-[0.2em] text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-all group">
                    <x-ri-information-line class="size-3 mr-1" />
                    {{ $setting->description }}
                    <x-ri-arrow-right-up-line class="ml-1 size-3 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                </a>
            @else
                <div class="flex items-start gap-1.5">
                    <x-ri-information-line class="size-3 text-gray-400 dark:text-gray-500 mt-0.5 flex-shrink-0" />
                    <p class="text-[9px] font-medium text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $setting->description }}
                    </p>
                </div>
            @endisset
        </div>
    @endisset
    
    {{-- Error Message --}}
    @error("fields.{$key}.{$setting->name}")
        <p class="text-[10px] font-bold text-red-500 dark:text-red-400 flex items-center gap-1 mt-1">
            <x-ri-error-warning-line class="size-3" />
            {{ $message }}
        </p>
    @enderror
</div>