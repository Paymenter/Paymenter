<x-button.primary 
    {{ $attributes->merge([
        'class' => '!bg-error/90 !bg-none text-white !py-4 !px-8 !rounded-2xl 
                    hover:!bg-error hover:scale-[1.02] active:scale-[0.98] 
                    transition-all duration-300 shadow-xl shadow-error/20 
                    !border-none group relative overflow-hidden'
    ]) }}>
    
    <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

    <div class="flex items-center gap-2">
        {{ $slot }}
    </div>
</x-button.primary>