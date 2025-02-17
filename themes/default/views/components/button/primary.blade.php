<button 
    {{ $attributes->merge(['class' => 'bg-primary text-white font-semibold hover:bg-primary/80 py-2 px-4 rounded-md w-full duration-300 cursor-pointer']) }}>

    @if (isset($type) && $type === 'submit')
        <div role="status" wire:loading>
            <x-ri-loader-5-fill aria-hidden="true" class="size-6 me-2 fill-background animate-spin" />
            <span class="sr-only">Loading...</span>
        </div>
        <div wire:loading.remove>
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</button>
