@props([
    'label',
    'id' => 'toggle-' . \Illuminate\Support\Str::random(8),
    'disabled' => false,
])

<label for="{{ $id }}" class="group flex items-center {{ $disabled ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }} animate-in fade-in duration-500">
    <div class="relative">
        <input
            id="{{ $id }}"
            type="checkbox"
            class="sr-only peer"
            {{ $attributes->except('disabled') }}
            {{ $disabled ? 'disabled' : '' }}
        >
        
        <div class="w-12 h-6 bg-white/5 backdrop-blur-md border border-neutral/20 rounded-full 
                    peer-checked:bg-primary/20 peer-checked:border-primary/40 
                    shadow-inner transition-all duration-300 ease-in-out">
        </div>

        <div class="absolute left-[3px] top-[3px] bg-white rounded-full h-[18px] w-[18px] 
                    shadow-[0_2px_4px_rgba(0,0,0,0.2)]
                    peer-checked:translate-x-[24px] peer-checked:bg-primary
                    peer-checked:shadow-[0_0_12px_rgba(var(--primary-rgb),0.6)]
                    transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]">
        </div>
    </div>

    @isset($label)
        <span class="ml-4 text-[10px] font-black uppercase tracking-[0.2em] text-base/40 group-hover:text-base/80 transition-colors">
            {{ $label }}
        </span>
    @endisset
</label>