<div class="flex flex-row items-center gap-4">
    <x-button.primary {{ $attributes }}>
        {{ $slot }}
    </x-button.primary>

    <div 
        id="saved" 
        class="hidden opacity-0 flex items-center gap-2 px-4 py-2 bg-brand-teal/10 border border-brand-teal/20 rounded-full transition-all duration-500 ease-out transform translate-x-[-10px]"
    >
        <x-ri-checkbox-circle-fill class="size-4 text-brand-teal animate-in zoom-in duration-300" />
        <span class="text-[10px] font-black text-brand-teal uppercase tracking-[0.2em]">
            {{ __('Saved!') }}
        </span>
    </div>

    @script
        <script>
            $wire.on('saved', function () {
                const el = document.getElementById('saved');
                
                // Show and Animate In
                el.classList.remove('hidden');
                setTimeout(() => {
                    el.classList.remove('opacity-0', 'translate-x-[-10px]');
                    el.classList.add('opacity-100', 'translate-x-0');
                }, 50);

                // Animate Out and Hide
                setTimeout(() => {
                    el.classList.replace('opacity-100', 'opacity-0');
                    el.classList.add('translate-x-[10px]');
                }, 2500);

                setTimeout(() => {
                    el.classList.add('hidden');
                    el.classList.remove('translate-x-[10px]');
                    el.classList.add('translate-x-[-10px]');
                }, 3100);
            });
        </script>
    @endscript
</div>