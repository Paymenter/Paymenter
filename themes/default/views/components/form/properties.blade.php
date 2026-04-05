@props(['properties', 'custom_properties' => []])

<div class="flex flex-col gap-6 animate-in fade-in duration-700">
    @foreach ($custom_properties as $property)
        <div class="flex flex-col gap-2 group/property">
            @switch($property->type)
                {{-- Date, String, Number Inputs --}}
                @case('date') 
                @case('string') 
                @case('number')
                    <x-form.input 
                        :type="$property->type" 
                        name="properties.{{ $property->key }}" 
                        :label="$property->name" 
                        :required="$property->required"
                        wire:model="properties.{{ $property->key }}" 
                        :value="$properties[$property->key] ?? ''" 
                        :disabled="$property->non_editable && isset($properties[$property->key])"
                        :helper="$property->description ?? null"
                        class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                        divClass="w-full"
                    />
                @break

                {{-- Checkbox --}}
                @case('checkbox')
                    <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <x-form.checkbox 
                            name="properties.{{ $property->key }}" 
                            :label="$property->name" 
                            :required="$property->required"
                            wire:model="properties.{{ $property->key }}" 
                            :checked="$properties[$property->key] ?? false" 
                            :disabled="$property->non_editable && isset($properties[$property->key])" 
                        />
                        @if($property->description)
                            <p class="mt-2 text-[9px] font-medium text-gray-500 dark:text-gray-400">{{ $property->description }}</p>
                        @endif
                    </div>
                @break

                {{-- Radio Group --}}
                @case('radio')
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            {{ $property->name }}
                            @if($property->required)
                                <span class="text-red-500 dark:text-red-400 ml-0.5">*</span>
                            @endif
                        </label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($property->allowed_values as $value)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer transition-all duration-200 hover:border-primary-300 dark:hover:border-primary-700 has-[:checked]:border-primary-500 dark:has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/10 dark:has-[:checked]:bg-primary-950/20">
                                    <div class="relative flex items-center">
                                        <input type="radio" 
                                               value="{{ $value }}" 
                                               name="properties.{{ $property->key }}"
                                               @checked(($properties[$property->key] ?? null) === $value) 
                                               @required($property->required)
                                               wire:model="properties.{{ $property->key }}" 
                                               @if($property->non_editable && isset($properties[$property->key])) disabled @endif
                                               class="peer appearance-none size-5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-full 
                                                      checked:bg-primary-500 checked:border-primary-500
                                                      focus:ring-2 focus:ring-primary-500/30
                                                      transition-all duration-200 cursor-pointer
                                                      disabled:opacity-50 disabled:cursor-not-allowed" 
                                        />
                                        <div class="absolute size-2 bg-white rounded-full opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 left-1.5 top-1.5 pointer-events-none transition-all duration-200"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $value }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($property->description)
                            <p class="text-[9px] font-medium text-gray-500 dark:text-gray-400 mt-2">{{ $property->description }}</p>
                        @endif
                    </div>
                @break

                {{-- Select Dropdown --}}
                @case('select')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            {{ $property->name }}
                            @if($property->required)
                                <span class="text-red-500 dark:text-red-400 ml-0.5">*</span>
                            @endif
                        </label>
                        <select 
                            name="properties.{{ $property->key }}"
                            wire:model="properties.{{ $property->key }}"
                            @if($property->required) required @endif
                            @if($property->non_editable && isset($properties[$property->key])) disabled @endif
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm font-medium text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 dark:focus:border-primary-500 focus:outline-none
                                   transition-all duration-200
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select {{ $property->name }}</option>
                            @foreach ($property->allowed_values as $value)
                                <option value="{{ $value }}" @selected(($properties[$property->key] ?? null) === $value)>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @if($property->description)
                            <p class="text-[9px] font-medium text-gray-500 dark:text-gray-400">{{ $property->description }}</p>
                        @endif
                    </div>
                @break

                {{-- Textarea --}}
                @case('text')
                    <x-form.textarea 
                        name="properties.{{ $property->key }}" 
                        :label="$property->name" 
                        :required="$property->required"
                        wire:model="properties.{{ $property->key }}" 
                        :disabled="$property->non_editable && isset($properties[$property->key])"
                        :helper="$property->description ?? null"
                        class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                        rows="4"
                    >
                        {{ $properties[$property->key] ?? '' }}
                    </x-form.textarea>
                @break

                {{-- Color Picker --}}
                @case('color')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            {{ $property->name }}
                            @if($property->required)
                                <span class="text-red-500 dark:text-red-400 ml-0.5">*</span>
                            @endif
                        </label>
                        <div class="flex items-center gap-3">
                            <input 
                                type="color"
                                name="properties.{{ $property->key }}"
                                wire:model="properties.{{ $property->key }}"
                                value="{{ $properties[$property->key] ?? '#000000' }}"
                                @if($property->required) required @endif
                                @if($property->non_editable && isset($properties[$property->key])) disabled @endif
                                class="w-12 h-12 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                            />
                            <x-form.input 
                                type="text"
                                name="properties.{{ $property->key }}_text"
                                wire:model="properties.{{ $property->key }}"
                                :value="$properties[$property->key] ?? ''"
                                class="flex-1 !bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl"
                                placeholder="#000000"
                            />
                        </div>
                        @if($property->description)
                            <p class="text-[9px] font-medium text-gray-500 dark:text-gray-400">{{ $property->description }}</p>
                        @endif
                    </div>
                @break

                {{-- Email --}}
                @case('email')
                    <x-form.input 
                        type="email"
                        name="properties.{{ $property->key }}" 
                        :label="$property->name" 
                        :required="$property->required"
                        wire:model="properties.{{ $property->key }}" 
                        :value="$properties[$property->key] ?? ''" 
                        :disabled="$property->non_editable && isset($properties[$property->key])"
                        :helper="$property->description ?? null"
                        class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                        divClass="w-full"
                    />
                @break

                {{-- URL --}}
                @case('url')
                    <x-form.input 
                        type="url"
                        name="properties.{{ $property->key }}" 
                        :label="$property->name" 
                        :required="$property->required"
                        wire:model="properties.{{ $property->key }}" 
                        :value="$properties[$property->key] ?? ''" 
                        :disabled="$property->non_editable && isset($properties[$property->key])"
                        :helper="$property->description ?? null"
                        class="!bg-white dark:!bg-gray-800 !border-gray-200 dark:!border-gray-700 !rounded-xl focus:!ring-2 focus:!ring-primary-500"
                        divClass="w-full"
                    />
                @break

                {{-- Default / Unknown Type --}}
                @default
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-950/30 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                        <p class="text-xs text-yellow-700 dark:text-yellow-400">
                            Unknown property type: <strong>{{ $property->type }}</strong> for field "{{ $property->name }}"
                        </p>
                    </div>
            @endswitch
        </div>
    @endforeach
</div>