<div class="flex items-center {{ $divClass ?? '' }}">
    {{-- <input type="hidden" name="{{ $name }}" value="0" /> --}}
    <input type="checkbox" name="{{ $name }}" id="{{ $id ?? $name }}"
        {{ $attributes->only(['wire:model', 'checked', 'wire:dirty.class']) }}
        class="form-checkbox w-4 h-4 text-primary rounded focus:ring-secondary hover:bg-secondary ring-offset-primary-800 focus:ring-2 bg-background-secondary border-neutral" />
    <label class="ml-2 text-sm text-primary-100" for="{{ $id ?? $name }}">{{ $label }}</label>

    @error($name)
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>
