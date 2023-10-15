@if ($type == 'checkbox')
    <div @isset($class) class={{ $class }} @endisset>
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="{{ $id ?? $name }}" type="checkbox"
                    @isset($value) value={{ $value }} @endisset
                    @isset($checked) {{ $checked ? 'checked' : '' }} @endisset
                    name={{ $name }}
                    autocomplete="@isset($autocomplete) {{ $autocomplete }} @else {{ $type }} @endisset"
                    @isset($disabled) {{ $disabled ? 'disabled' : '' }} @endisset
                    class="w-4 h-4 border border-secondary-300 rounded bg-secondary-200 text-primary-400 focus:ring-3 focus:ring-primary-300 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100"
                    @isset($required) {{ $required ? 'required' : '' }} @endisset>
            </div>
            <label for="{{ $id ?? $name }}"
                class="ml-2 text-sm font-medium text-secondary-700">{!! $label !!}</label>
        </div>
    </div>
@elseif($type == 'color')
    <div @isset($class) class={{ $class }} @endisset>
        {{ $slot }}
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{!! $label !!}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <input type={{ $type }}
                @isset($placeholder) placeholder={{ $placeholder }} @endisset
                name={{ $name }}
                autocomplete="@isset($autocomplete) {{ $autocomplete }} @else {{ $type }} @endisset"
                @isset($value) value={{ $value }} @else value="{{ old($name) }}" @endisset
                id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror">
        </div>
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@elseif($type == 'select')
    <div @isset($class) class="{{ $class }}" @endisset>
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{!! $label !!}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <select {{ $attributes->except('class') }} id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror
            @isset($icon) pl-10 pr-4 @else px-4 @endisset">
                {{ $slot }}
            </select>
        </div>
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@elseif($type == 'textarea')
    <div @isset($class) class="{{ $class }}" @endisset>
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{!! $label !!}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <textarea {{ $attributes->except('class') }} id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror">{{ $slot }}</textarea>
        </div>
        @error($name)
            <label for="{{ $id ?? $name }}" class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@else
    <div @isset($class) class="{{ $class }}" @endisset>
        {{ $slot }}
        @isset($label)
            <label for="{{ $id ?? $name }}" class="text-sm text-secondary-600">{{ $label }}</label>
        @endisset
        <div class="relative">
            @isset($icon)
                <div class="absolute pointer-events-none top-0 left-0 py-2 px-4">
                    <i class="{{ $icon }}"></i>
                </div>
            @endisset
            <input type={{ $type }} {{ $attributes->except('class') }}
                @isset($value) value="{{ $value }}" @else value="{{ old($name) }}" @endisset
                id={{ $id ?? $name }}
                @isset($required) {{ $required ? 'required' : '' }} @endisset
                class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300
            @isset($icon) pl-10 pr-4 @else px-4 @endisset
            @error($name) border-danger-300 focus:border-danger-400 focus:ring-danger-300 @else border-secondary-300 focus:border-secondary-400 focus:ring-primary-400 @enderror">
        </div>
        @error($name)
            <label for={{ $id ?? $name }} class="text-sm text-danger-300">{{ $message }}</label>
        @enderror
    </div>
@endif
