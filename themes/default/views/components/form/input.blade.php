@props([
    'name', 
    'label' => null, 
    'required' => false, 
    'divClass' => null, 
    'class' => null,
    'placeholder' => null, 
    'id' => null, 
    'type' => null, 
    'hideRequiredIndicator' => false, 
    'dirty' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'helper' => null
])

@php
    $inputId = $id ?? $name;
    $hasIcon = !empty($icon);
    
    // Adjusted padding for the "Big" input style
    $paddingClass = match(true) {
        $hasIcon && $iconPosition === 'left' => 'pl-12 pr-5',
        $hasIcon && $iconPosition === 'right' => 'pl-5 pr-12',
        default => 'px-5'
    };
@endphp

<fieldset class="group flex flex-col w-full {{ $divClass ?? '' }} animate-in fade-in duration-500">
    @if ($label)
        <label for="{{ $inputId }}" class="mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-base/40 group-focus-within:text-primary transition-colors duration-300 flex items-center gap-1">
            {{ $label }}
            @if ($required && !$hideRequiredIndicator)
                <span class="text-error ml-1">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        {{-- Left Icon --}}
        @if($hasIcon && $iconPosition === 'left')
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-base/30 group-focus-within:text-primary transition-colors duration-300 z-10">
                <x-dynamic-component :component="$icon" class="size-5" />
            </div>
        @endif

        <input 
            type="{{ $type ?? 'text' }}" 
            id="{{ $inputId }}" 
            name="{{ $name }}"
            placeholder="{{ $placeholder ?? ($label ?? '') }}"
            @if ($dirty && isset($attributes['wire:model'])) 
                wire:dirty.class="!border-amber-500/50 !ring-1 !ring-amber-500/20 shadow-[0_0_15px_rgba(245,158,11,0.1)]" 
            @endif
            @required($required)
            {{ $attributes->except(['placeholder', 'label', 'id', 'name', 'type', 'class', 'divClass', 'required', 'hideRequiredIndicator', 'dirty', 'icon', 'iconPosition']) }}
            class="block w-full text-xs font-bold text-base bg-white/5 backdrop-blur-md border border-neutral/20 rounded-xl 
                   shadow-inner focus:ring-4 focus:ring-primary/10 focus:border-primary/40 focus:outline-none 
                   transition-all duration-300 ease-in-out 
                   placeholder:text-base/20 placeholder:uppercase placeholder:tracking-widest
                   disabled:opacity-40 disabled:cursor-not-allowed
                   {{ $paddingClass }} py-4
                   {{ $class ?? '' }}" 
        />

        {{-- Right Icon --}}
        @if($hasIcon && $iconPosition === 'right')
            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-base/30 group-focus-within:text-primary transition-colors duration-300 z-10">
                <x-dynamic-component :component="$icon" class="size-5" />
            </div>
        @endif

        {{-- Focus Glow Effect --}}
        <div class="absolute inset-0 rounded-xl bg-primary/5 opacity-0 group-focus-within:opacity-100 pointer-events-none transition-opacity duration-500"></div>
        
        {{-- Loading Indicator (for Livewire) --}}
        @if(isset($attributes['wire:model']))
            <div wire:loading wire:target="{{ $attributes['wire:model'] }}" class="absolute {{ $iconPosition === 'right' ? 'right-12' : 'right-4' }} top-1/2 -translate-y-1/2">
                <div class="size-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
            </div>
        @endif
    </div>

    @error($name)
        <p class="mt-2 text-[9px] font-black text-error uppercase tracking-widest animate-in slide-in-from-top-1 flex items-center">
            <x-ri-error-warning-line class="inline-block size-3 mr-1" />
            {{ $message }}
        </p>
    @enderror
    
    {{-- Helper Text --}}
    @if($helper)
        <p class="mt-2 text-[9px] font-black uppercase tracking-widest text-base/30 flex items-center gap-1">
            <x-ri-information-line class="size-3" />
            {{ $helper }}
        </p>
    @endif
</fieldset>