<button 
    {{ $attributes->merge([
        'class' => 'group relative flex items-center gap-2 justify-center bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] 
                    hover:bg-primary/90 hover:scale-[1.02] active:scale-[0.98] 
                    py-4 px-8 rounded-2xl w-full transition-all duration-300 
                    shadow-xl shadow-primary/20 cursor-pointer 
                    disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100'
    ]) }}>
    
    @if (isset($type) && $type === 'submit')
        <div role="status" wire:loading wire:target="{{ $attributes->get('wire:click') }}">
            <x-ri-loader-5-fill aria-hidden="true" class="size-4 animate-spin fill-white" />
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

    <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
</button>