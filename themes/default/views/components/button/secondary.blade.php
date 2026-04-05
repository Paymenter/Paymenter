<button 
    {{ $attributes->merge([
        'class' => 'group relative flex items-center gap-2 justify-center 
                    bg-white/40 backdrop-blur-md border border-neutral/30 
                    text-base/60 text-[10px] font-black uppercase tracking-[0.2em] 
                    hover:bg-white/60 hover:text-base hover:border-neutral/60 hover:scale-[1.02] 
                    active:scale-[0.98] py-4 px-8 rounded-2xl w-full 
                    transition-all duration-300 shadow-sm cursor-pointer 
                    disabled:cursor-not-allowed disabled:opacity-50'
    ]) }}>
    
    @if (isset($type) && $type === 'submit')
        <div role="status" wire:loading wire:target="{{ $attributes->get('wire:click') }}">
            <x-ri-loader-5-fill aria-hidden="true" class="size-4 animate-spin fill-base/40" />
            <span class="sr-only">Loading...</span>
        </div>

        <div wire:loading.remove wire:target="{{ $attributes->get('wire:click') }}" class="flex items-center gap-2">
            {{ $slot }}
        </div>
    @else
        <div class="flex items-center gap-2">
            {{ $slot }}
        </div>
    @endif

    <div class="absolute inset-0 rounded-2xl bg-gradient-to-b from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
</button>