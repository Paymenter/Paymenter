<div>
    @if ($isDebugEnabled)
        <div class="fi-widget-wrapper mt-6 mb-2 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 shadow-sm dark:border-white/10">
            <div class="flex items-center gap-3">
                <x-ri-error-warning-line class="size-5 shrink-0 text-yellow-500" />
                <p class="text-sm text-gray-950 dark:text-white">
                    <span class="font-medium">Debug Mode is enabled.</span>
                    <span class="text-gray-500 dark:text-gray-400">
                        HTTP requests and application errors are currently being logged to the database.
                    </span>
                </p>
            </div>

            @if ($canUpdateSettings)
                <x-filament::button
                    color="danger"
                    size="sm"
                    icon="ri-shut-down-line"
                    wire:click="disableDebugMode"
                    wire:loading.attr="disabled"
                >
                    Disable Debug Mode
                </x-filament::button>
            @endif
        </div>
    @endif
</div>
