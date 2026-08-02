<button
    {{ $attributes->merge(['class' => 'cyber-clip-sm relative flex items-center gap-2 justify-center bg-primary text-white text-sm font-bold uppercase tracking-wide hover:bg-primary/85 py-2.5 lg:py-2 px-4.5 rounded-md w-full duration-300 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50 shadow-[0_0_18px_-6px_hsl(var(--color-primary))] hover:shadow-[0_0_26px_-4px_hsl(var(--color-primary))]']) }}>
    @if (isset($type) && $type === 'submit')
        <div role="status" wire:loading>
            <x-ri-loader-5-fill aria-hidden="true" class="size-6 me-2 fill-background animate-spin" />
            <span class="sr-only">Loading...</span>
        </div>
        <div wire:loading.remove class="flex items-center gap-2">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</button>
