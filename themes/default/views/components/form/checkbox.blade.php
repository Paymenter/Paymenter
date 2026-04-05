@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'divClass' => null,
    'required' => false,
    'checked' => false,
    'disabled' => false
])

@php
    $checkboxId = $id ?? $name ?? uniqid('checkbox_');
@endphp

<div class="group flex flex-col {{ $divClass }}">
    <div class="flex items-center cursor-pointer">
        <div class="relative flex items-center">
            <input 
                type="checkbox" 
                name="{{ $name }}" 
                id="{{ $checkboxId }}"
                @if($checked) checked @endif
                @if($disabled) disabled @endif
                {{ $attributes->except(['label', 'name', 'id', 'class', 'divClass', 'required', 'checked', 'disabled']) }}
                class="peer appearance-none size-5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg 
                       checked:bg-primary-500 checked:border-primary-500 dark:checked:bg-primary-500 dark:checked:border-primary-500
                       hover:border-primary-400 dark:hover:border-primary-500
                       focus:ring-2 focus:ring-primary-500/30 focus:ring-offset-0
                       disabled:opacity-50 disabled:cursor-not-allowed
                       transition-all duration-200 cursor-pointer" />
            
            {{-- Check Icon --}}
            <x-ri-check-line 
                class="absolute size-4 text-white opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 
                       left-0.5 top-0.5 pointer-events-none transition-all duration-200 ease-out" 
            />
            
            {{-- Indeterminate Icon (if needed) --}}
            <x-ri-subtract-line 
                class="absolute size-4 text-white opacity-0 scale-50 peer-indeterminate:opacity-100 peer-indeterminate:scale-100 
                       left-0.5 top-0.5 pointer-events-none transition-all duration-200 ease-out hidden" 
            />
        </div>

        <label class="ml-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-[0.1em] group-hover:text-primary-600 dark:group-hover:text-primary-400 cursor-pointer transition-colors duration-200 
                      {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
               for="{{ $checkboxId }}">
            @if($label)
                {{ $label }}
                @if($required)
                    <span class="text-red-500 ml-0.5">*</span>
                @endif
            @else
                {{ $slot }}
                @if($required)
                    <span class="text-red-500 ml-0.5">*</span>
                @endif
            @endif
        </label>
    </div>

    @error($name)
        <p class="mt-2 text-[9px] font-bold text-red-500 dark:text-red-400 uppercase tracking-wider animate-in slide-in-from-top-1 flex items-center gap-1">
            <x-ri-error-warning-line class="size-3" />
            {{ $message }}
        </p>
    @enderror
</div>

{{-- Optional: CSS for indeterminate state support --}}
@push('scripts')
<script>
    // Support for indeterminate state
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        if (checkbox.indeterminate) {
            checkbox.classList.add('indeterminate');
        }
    });
</script>
@endpush