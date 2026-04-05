@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'multiple' => false,
    'required' => false,
    'divClass' => null,
    'hideRequiredIndicator' => false,
    'placeholder' => null,
])

<fieldset class="group flex flex-col w-full {{ $divClass ?? '' }} animate-in fade-in duration-500">
    @if ($label)
        <label for="{{ $id ?? $name }}" class="mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-base/40 group-focus-within:text-primary transition-colors duration-300">
            {{ $label }}
            @if ($required && !$hideRequiredIndicator)
                <span class="text-error ml-1">*</span>
            @endif
        </label>
    @endif

    <div class="relative group">
        <select 
            id="{{ $id ?? $name }}" 
            name="{{ $name }}"
            {{ $multiple ? 'multiple' : '' }} 
            @required($required)
            {{ $attributes->except(['options', 'id', 'name', 'multiple', 'class', 'placeholder']) }}
            class="block w-full text-xs font-bold text-base bg-white/5 backdrop-blur-md border border-neutral/20 rounded-xl 
                   shadow-inner focus:ring-4 focus:ring-primary/10 focus:border-primary/40 focus:outline-none 
                   transition-all duration-300 ease-in-out appearance-none
                   disabled:opacity-40 disabled:cursor-not-allowed 
                   @if ($multiple) min-h-[120px] p-2 @else px-5 py-4 @endif"
        >
            @if (!$multiple && $placeholder)
                <option value="" disabled {{ is_null($selected) ? 'selected' : '' }} class="bg-neutral-900 text-base/50">
                    {{ $placeholder }}
                </option>
            @endif

            @if (count($options) == 0 && isset($slot) && $slot->isNotEmpty())
                {{ $slot }}
            @else
                @foreach ($options as $key => $option)
                    @php 
                        // Clean logic to avoid compilation errors
                        $isSelected = ($multiple && is_array($selected)) 
                            ? in_array($key, $selected) 
                            : ($selected == $key);
                    @endphp
                    <option value="{{ $key }}" {{ $isSelected ? 'selected' : '' }} class="bg-neutral-900 text-white py-2">
                        {{ $option }}
                    </option>
                @endforeach
            @endif
        </select>

        {{-- Custom Arrow --}}
        @if (!$multiple)
            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-base/30 group-focus-within:text-primary transition-colors duration-300">
                <x-ri-arrow-down-s-line class="size-5" />
            </div>
        @endif

        {{-- Focus Glow --}}
        <div class="absolute inset-0 rounded-xl bg-primary/5 opacity-0 group-focus-within:opacity-100 pointer-events-none transition-opacity duration-500"></div>
    </div>

    @if ($multiple)
        <div class="mt-2 flex items-center gap-2">
            <x-ri-information-line class="size-3 text-primary/60" />
            <p class="text-[9px] font-black uppercase tracking-widest text-base/40">
                {{ __('Hold Ctrl / ⌘ to select multiple') }}
            </p>
        </div>
    @endif

    @error($name)
        <p class="mt-2 text-[9px] font-black text-error uppercase tracking-widest animate-in slide-in-from-top-1 flex items-center">
            <x-ri-error-warning-line class="inline-block size-3 mr-1" />
            {{ $message }}
        </p>
    @enderror
</fieldset>