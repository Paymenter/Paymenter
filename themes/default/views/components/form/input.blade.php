@props([
    'name',
    'label' => null,
    'required' => false,
    'divClass' => null,
    'placeholder' => null,
    'id' => null,
    'hideRequiredIndicator' => false,
])
<fieldset class="flex flex-col relative mt-3 w-full {{ $divClass ?? '' }}">
    @if ($label)
        <legend>
            <label for="{{ $name }}"
                class="text-sm text-dark-text dark:text-light-text absolute -translate-y-1/2 start-1 ml-1 bg-white dark:bg-primary-800 px-2">
                {{ $label }}
                @if ($required && !$hideRequiredIndicator)
                    <span class="text-red-500">*</span>
                @endif
            </label>
        </legend>
    @endif
    <input type="{{ $type ?? 'text' }}" id="{{ $id ?? $name }}" name="{{ $name }}"
        class="block px-2.5 py-2.5 w-full text-sm text-dark-text dark:text-light-text bg-white dark:bg-primary-800 border-2 border-primary-300 dark:border-primary-700 rounded-md outline-none focus:outline-none focus:border-secondary dark:focus:border-secondary transition-all duration-300 ease-in-out"
        placeholder="{{ $placeholder ?? ($label ?? '') }}"
        {{ $attributes->only(['wire:model', 'required', 'value']) }} />
    @error($name)
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</fieldset>
