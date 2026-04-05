@props([
    'name',
    'label' => null,
    'id' => null,
    'disabled' => false,
    'required' => false,
    'size' => 'md', // sm, md, lg
    'color' => 'primary',
    'labelPosition' => 'right', // left, right
    'description' => null,
])

@php
    $toggleId = $id ?? 'toggle-' . $name ?? \Illuminate\Support\Str::random(8);
    $hasError = $errors->has($name);
    
    $sizes = [
        'sm' => [
            'switch' => 'w-8 h-4',
            'knob' => 'h-3 w-3',
            'translate' => 'translate-x-[16px]',
            'knob_position' => 'left-[2px] top-[2px]',
            'label' => 'text-xs'
        ],
        'md' => [
            'switch' => 'w-11 h-6',
            'knob' => 'h-5 w-5',
            'translate' => 'translate-x-[20px]',
            'knob_position' => 'left-[2px] top-[2px]',
            'label' => 'text-sm'
        ],
        'lg' => [
            'switch' => 'w-14 h-7',
            'knob' => 'h-6 w-6',
            'translate' => 'translate-x-[28px]',
            'knob_position' => 'left-[2px] top-[2px]',
            'label' => 'text-base'
        ],
    ];
    
    $sizeConfig = $sizes[$size] ?? $sizes['md'];
    
    $colors = [
        'primary' => 'peer-checked:bg-primary-500 peer-checked:border-primary-500',
        'success' => 'peer-checked:bg-green-500 peer-checked:border-green-500',
        'danger' => 'peer-checked:bg-red-500 peer-checked:border-red-500',
        'warning' => 'peer-checked:bg-yellow-500 peer-checked:border-yellow-500',
        'info' => 'peer-checked:bg-blue-500 peer-checked:border-blue-500',
    ];
    
    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div class="flex flex-col gap-2 animate-in fade-in duration-500">
    <div class="flex items-center {{ $labelPosition === 'left' ? 'flex-row-reverse justify-between' : 'flex-row' }}">
        @if($labelPosition === 'left')
            <label for="{{ $toggleId }}" class="ml-4 {{ $sizeConfig['label'] }} font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                {{ $label }}
                @if($required)
                    <span class="text-red-500 ml-0.5">*</span>
                @endif
            </label>
        @endif
        
        <label for="{{ $toggleId }}" class="group relative inline-flex items-center {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
            <input
                id="{{ $toggleId }}"
                name="{{ $name }}"
                type="checkbox"
                class="sr-only peer"
                {{ $attributes->except(['disabled', 'label', 'size', 'color', 'labelPosition', 'description']) }}
                {{ $disabled ? 'disabled' : '' }}
                @if($required) required @endif
            >
            
            {{-- Switch Track --}}
            <div class="{{ $sizeConfig['switch'] }} bg-gray-300 dark:bg-gray-600 border border-gray-400 dark:border-gray-500 rounded-full 
                        peer-checked:{{ $colorClass }}
                        transition-all duration-300 ease-in-out
                        {{ $hasError ? 'border-red-500 dark:border-red-500 ring-2 ring-red-500/20' : '' }}">
            </div>

            {{-- Switch Knob --}}
            <div class="absolute {{ $sizeConfig['knob_position'] }} bg-white rounded-full shadow-md
                        peer-checked:{{ $sizeConfig['translate'] }} peer-checked:bg-white
                        transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]
                        {{ $sizeConfig['knob'] }}">
            </div>
            
            {{-- Loading Indicator --}}
            @if(isset($attributes['wire:model']))
                <div wire:loading wire:target="{{ $attributes['wire:model'] }}" class="absolute inset-0 flex items-center justify-center">
                    <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                </div>
            @endif
        </label>
        
        @if($labelPosition === 'right')
            <label for="{{ $toggleId }}" class="ml-3 {{ $sizeConfig['label'] }} font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                {{ $label }}
                @if($required)
                    <span class="text-red-500 ml-0.5">*</span>
                @endif
            </label>
        @endif
    </div>
    
    {{-- Description --}}
    @if($description)
        <p class="text-[10px] text-gray-500 dark:text-gray-400 ml-0.5">
            {{ $description }}
        </p>
    @endif
    
    {{-- Error Message --}}
    @error($name)
        <p class="text-[10px] font-bold text-red-500 dark:text-red-400 flex items-center gap-1 mt-1">
            <x-ri-error-warning-line class="size-3" />
            {{ $message }}
        </p>
    @enderror
</div>