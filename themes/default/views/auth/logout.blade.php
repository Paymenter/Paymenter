<div>
    <button wire:click="logout"
        class="flex flex-row items-center p-3 gap-2 text-sm font-semibold text-error/80 hover:text-error bg-background-secondary hover:bg-background-secondary/80"
        wire:loading.attr="disabled">
        <div role="status" wire:loading>
            <x-ri-loader-5-fill aria-hidden="true" class="size-6 me-2 fill-base animate-spin" />
            <span class="sr-only">Loading...</span>
        </div>
        <div wire:loading.remove>
            {{ __('auth.logout') }}
        </div>
    </button>
</div>