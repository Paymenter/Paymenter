{{-- Toast notifications --}}
<div
    x-data
    class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
    aria-live="polite"
>
    <template x-for="notification in $store.notifications.notifications" :key="notification.id">
        <div
            x-show="notification.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto flex items-start gap-3 p-4 rounded-2xl
                   bg-background-secondary/95 backdrop-blur-xl border border-neutral/50
                   shadow-2xl shadow-black/20"
        >
            {{-- Icon --}}
            <div class="flex-shrink-0 mt-0.5">
                <div x-show="notification.type === 'success'"
                     class="h-5 w-5 rounded-full bg-success/20 flex items-center justify-center">
                    <svg class="h-3 w-3 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div x-show="notification.type === 'error'"
                     class="h-5 w-5 rounded-full bg-error/20 flex items-center justify-center">
                    <svg class="h-3 w-3 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div x-show="notification.type === 'warning'"
                     class="h-5 w-5 rounded-full bg-warning/20 flex items-center justify-center">
                    <svg class="h-3 w-3 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01" />
                    </svg>
                </div>
                <div x-show="!['success','error','warning'].includes(notification.type)"
                     class="h-5 w-5 rounded-full bg-info/20 flex items-center justify-center">
                    <svg class="h-3 w-3 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01" />
                    </svg>
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-base" x-text="notification.title"></p>
                <p class="text-xs text-muted mt-0.5" x-text="notification.message"></p>
            </div>

            {{-- Dismiss --}}
            <button
                @click="$store.notifications.removeNotification(notification.id)"
                class="flex-shrink-0 p-1 rounded-lg hover:bg-neutral/30 transition-colors text-muted hover:text-base"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
