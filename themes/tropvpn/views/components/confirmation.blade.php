{{-- Confirmation modal --}}
<div
    x-data
    x-show="$store.confirmation.show"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    @keydown.escape.window="$store.confirmation.close()"
>
    {{-- Backdrop --}}
    <div
        x-show="$store.confirmation.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        @click="$store.confirmation.close()"
    ></div>

    {{-- Dialog --}}
    <div
        x-show="$store.confirmation.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-md rounded-2xl bg-background-secondary border border-neutral/50
               shadow-2xl shadow-black/40 p-6"
    >
        <h3 class="text-lg font-bold mb-2" x-text="$store.confirmation.title"></h3>
        <p class="text-sm text-muted mb-6" x-text="$store.confirmation.message"></p>

        <div class="flex gap-3 justify-end">
            <button
                @click="$store.confirmation.close()"
                class="px-4 py-2 rounded-xl text-sm font-medium
                       bg-background-secondary border border-neutral hover:border-primary/30
                       transition-all"
                x-text="$store.confirmation.cancelText"
            ></button>
            <button
                @click="$store.confirmation.execute()"
                :disabled="$store.confirmation.loading"
                class="px-4 py-2 rounded-xl text-sm font-semibold
                       bg-error text-white hover:opacity-90
                       disabled:opacity-50 disabled:cursor-not-allowed
                       transition-all"
                x-text="$store.confirmation.loading ? 'Loading...' : $store.confirmation.confirmText"
            ></button>
        </div>
    </div>
</div>
