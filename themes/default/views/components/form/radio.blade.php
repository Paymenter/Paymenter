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
<fieldset class="flex flex-col w-full {{ $divClass ?? '' }}" name="{{ $name }}">
    @if ($label)
    <label for="{{ $name }}" class="mb-1 text-sm text-primary-100">
        {{ $label }}
        @if ($required && !$hideRequiredIndicator)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <div
        class="block px-2.5 py-2.5 w-full text-sm text-primary-100 bg-background-secondary border-2 border-neutral rounded-md outline-none focus:outline-none focus:border-secondary transition-all duration-300 ease-in-out">
        @if (count($options) == 0 && $slot)
        {{ $slot }}
        @else
        @foreach ($options as $key => $option)
        <div class="flex items-center gap-2">
            <input type="radio" id="{{ $name }}_{{ $key }}" name="{{ $name }}"
                value="{{ gettype($options) == 'array' ? $option : $key }}" {{ ($multiple && $selected ? in_array($key,
                $selected) : $selected==$option) ? 'checked' : '' }} />
            <label for="{{ $name }}_{{ $key }}">
                {{ $option }}
            </label>
        </div>
        @endforeach
        @endif
    </div>

    @error($name)
    <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</fieldset>