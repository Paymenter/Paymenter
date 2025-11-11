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
])
<fieldset class="flex flex-col w-full {{ $divClass ?? '' }}">
    @if ($label)
    <label for="{{ $name }}" class="mb-1 text-sm text-primary-100">
        {{ $label }}
        @if ($required && !$hideRequiredIndicator)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    <textarea type="{{ $type ?? 'text' }}" id="{{ $id ?? $name }}" name="{{ $name }}"
        class="block w-full text-sm text-primary-100 bg-primary-800 border-2 border-neutral rounded-md outline-none focus:outline-none focus:border-secondary transition-all duration-300 ease-in-out disabled:bg-primary-700 disabled:cursor-not-allowed {{ $class ?? '' }} @if ($type !== 'color') px-2.5 py-2.5 @endif"
        placeholder="{{ $placeholder ?? ($label ?? '') }}" @if ($dirty && isset($attributes['wire:model']))
        wire:dirty.class="!border-yellow-600" @endif {{ $attributes->except(['placeholder', 'label', 'id', 'name', 'type', 'class', 'divClass', 'required', 'hideRequiredIndicator', 'dirty']) }}
        @required($required)>{{ $slot }}</textarea>
    @error($name)
    <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</fieldset>