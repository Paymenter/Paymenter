<button
    {{ $attributes->merge(['class' => 'cyber-clip-sm flex items-center gap-2 justify-center bg-background-secondary/70 text-sm font-bold uppercase tracking-wide border border-neutral hover:border-primary/70 hover:text-primary py-2.5 lg:py-2 px-4.5 rounded-md w-full duration-300 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50']) }}>
    @if (isset($type) && $type === 'submit')
        <div role="status" wire:loading>
            <x-ri-loader-5-fill aria-hidden="true" class="size-6 me-2 fill-current animate-spin" />
            <span class="sr-only">Loading...</span>
        </div>
        <div wire:loading.remove class="flex items-center gap-2">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</button>
