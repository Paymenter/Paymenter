@props([
'name',
'label' => null,
'options' => [],
'selected' => null,
'multiple' => false,
'required' => false,
'divClass' => null,
'hideRequiredIndicator' => false,
])
<fieldset class="flex flex-col relative mt-3 w-full {{ $divClass ?? '' }}">
    @if ($label)
    <legend>
        <label for="{{ $name }}"
            class="text-sm text-primary-100 absolute -translate-y-1/2 start-1 ml-1 bg-background-secondary rounded-md px-2">
            {{ $label }}
            @if ($required && !$hideRequiredIndicator)
            <span class="text-red-500">*</span>
            @endif
        </label>
    </legend>
    @endif

    <select id="{{ $id ?? $name }}" {{ $multiple ? 'multiple' : '' }} {{ $attributes->only(['required', 'wire:model',
        'wire:dirty.class', 'wire:model.live']) }}
        class="block px-2.5 py-2.5 w-full text-sm text-primary-100 bg-background-secondary border-2 border-neutral
        rounded-md outline-none focus:outline-none focus:border-secondary transition-all duration-300 ease-in-out">
        @if (count($options) == 0 && $slot)
        {{ $slot }}
        @else
        @foreach ($options as $key => $option)
        <option value="{{ gettype($options) == 'array' ? $option : $key }}" {{ ($multiple && $selected ? in_array($key,
            $selected) : $selected==$option) ? 'selected' : '' }}>
            {{ $option }}</option>
        @endforeach
        @endif
    </select>
    @if ($multiple)
    <p class="text-xs text-base">
        {{ __('Pro tip: Hold down the Ctrl (Windows) / Command (Mac) button to select multiple options.') }}</p>
    @endif

    @error($name)
    <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</fieldset>