@props([
    'label',
    'id' => 'toggle-' . \Illuminate\Support\Str::random(8),
    'disabled' => false,
])

<label for="{{ $id }}" class="flex items-center {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
    <div class="relative">
        <input
            id="{{ $id }}"
            type="checkbox"
            class="sr-only peer"
            {{ $attributes->except('disabled') }}
            {{ $disabled ? 'disabled' : '' }}
        >
        <div class="w-11 h-6 bg-neutral rounded-full peer-checked:bg-primary transition-colors duration-300 ease-in-out"></div>
        <div class="absolute left-[2px] top-[2px] bg-white border-none rounded-full h-5 w-5 
                    peer-checked:translate-x-full 
                    transition-transform duration-300 ease-in-out">
        </div>
    </div>
    @isset($label)
    <span class="ml-3 text-sm font-medium text-base/80">{{ $label }}</span>
    @endisset
</label>
