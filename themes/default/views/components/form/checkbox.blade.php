<div class="flex items-center {{ $divClass ?? '' }}">
    {{-- <input type="hidden" name="{{ $name }}" value="0" /> --}}
    <input type="checkbox" name="{{ $name }}" id="{{ $id ?? $name }}"
        {{ $attributes->only(['wire:model', 'checked']) }}
        class="form-checkbox w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-secondary hover:bg-secondary dark:ring-offset-primary-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
    <label class="ml-2 text-sm text-dark-text dark:text-light-text" for="{{ $id ?? $name }}">{{ $label }}</label>

    @error($name)
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>
