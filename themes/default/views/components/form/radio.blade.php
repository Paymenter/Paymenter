@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'multiple' => false,
    'required' => false,
    'divClass' => null,
    'hideRequiredIndicator' => false,
    'inline' => false,
    'cardStyle' => false,
])

@php
    $isChecked = false;
@endphp

<fieldset class="group flex flex-col w-full {{ $divClass }} animate-in fade-in duration-500" name="{{ $name }}">
    @if ($label)
        <label class="mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400 group-focus-within:text-primary-600 dark:group-focus-within:text-primary-400 transition-colors duration-200 flex items-center gap-1">
            {{ $label }}
            @if ($required && !$hideRequiredIndicator)
                <span class="text-red-500 dark:text-red-400 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="flex flex-col gap-2 w-full">
        @if (count($options) == 0 && $slot)
            <div class="px-3 py-2">
                {{ $slot }}
            </div>
        @else
            <div class="{{ $inline ? 'flex flex-wrap gap-3' : 'flex flex-col gap-2' }}">
                @foreach ($options as $key => $option)
                    @php 
                        $val = is_array($options) ? $option : $key;
                        $optionId = $name . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $val);
                        $isChecked = ($multiple && is_array($selected)) ? in_array($val, $selected) : ($selected == $val);
                    @endphp
                    
                    @if($cardStyle)
                        {{-- Card Style Radio --}}
                        <label for="{{ $optionId }}" 
                            class="relative flex items-center gap-4 p-4 rounded-xl cursor-pointer transition-all duration-200 
                                   border-2 hover:shadow-md
                                   {{ $isChecked 
                                        ? 'bg-primary-50 dark:bg-primary-950/30 border-primary-500 dark:border-primary-500 shadow-sm' 
                                        : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                            
                            <div class="relative flex items-center justify-center flex-shrink-0">
                                <input type="radio" 
                                    id="{{ $optionId }}" 
                                    name="{{ $name }}"
                                    value="{{ $val }}" 
                                    {{ $isChecked ? 'checked' : '' }}
                                    {{ $attributes->whereStartsWith('wire:model') }}
                                    @if($required) required @endif
                                    class="peer appearance-none size-5 border-2 border-gray-300 dark:border-gray-600 rounded-full 
                                           bg-white dark:bg-gray-800
                                           checked:border-primary-500 dark:checked:border-primary-500 
                                           focus:ring-2 focus:ring-primary-500/30 focus:ring-offset-0
                                           transition-all duration-200 cursor-pointer" 
                                />
                                
                                <div class="absolute size-2 bg-primary-500 rounded-full opacity-0 scale-50 
                                            peer-checked:opacity-100 peer-checked:scale-100 
                                            transition-all duration-200 ease-out pointer-events-none">
                                </div>
                            </div>

                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white transition-colors">
                                    {{ $option }}
                                </span>
                                @if(isset($descriptions[$val] ?? null))
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $descriptions[$val] }}</p>
                                @endif
                            </div>

                            @if($isChecked)
                                <x-ri-checkbox-circle-fill class="flex-shrink-0 size-5 text-primary-500 dark:text-primary-400 animate-in zoom-in duration-200" />
                            @endif
                        </label>
                    @else
                        {{-- Default Style Radio --}}
                        <label for="{{ $optionId }}" 
                            class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all duration-200 
                                   hover:bg-gray-50 dark:hover:bg-gray-800/50
                                   {{ $isChecked ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                            
                            <div class="relative flex items-center justify-center">
                                <input type="radio" 
                                    id="{{ $optionId }}" 
                                    name="{{ $name }}"
                                    value="{{ $val }}" 
                                    {{ $isChecked ? 'checked' : '' }}
                                    {{ $attributes->whereStartsWith('wire:model') }}
                                    @if($required) required @endif
                                    class="peer appearance-none size-4 border-2 border-gray-400 dark:border-gray-500 rounded-full 
                                           bg-white dark:bg-gray-800
                                           checked:border-primary-500 dark:checked:border-primary-500 
                                           focus:ring-2 focus:ring-primary-500/30
                                           transition-all duration-200 cursor-pointer" 
                                />
                                
                                <div class="absolute size-1.5 bg-primary-500 rounded-full opacity-0 scale-50 
                                            peer-checked:opacity-100 peer-checked:scale-100 
                                            transition-all duration-200 ease-out pointer-events-none">
                                </div>
                            </div>

                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors 
                                         {{ $isChecked ? 'text-primary-600 dark:text-primary-400 font-semibold' : '' }}">
                                {{ $option }}
                            </span>
                        </label>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @error($name)
        <p class="mt-2 text-[10px] font-bold text-red-500 dark:text-red-400 flex items-center gap-1 animate-in slide-in-from-top-1">
            <x-ri-error-warning-line class="size-3" />
            {{ $message }}
        </p>
    @enderror
</fieldset>