<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Status Card --}}
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div @class([
                        'flex h-12 w-12 items-center justify-center rounded-full',
                        'bg-warning-100 text-warning-600 dark:bg-warning-950 dark:text-warning-400' => $isDown,
                        'bg-success-100 text-success-600 dark:bg-success-950 dark:text-success-400' => ! $isDown,
                    ])>
                        @if ($isDown)
                            <x-heroicon-s-wrench-screwdriver class="h-6 w-6" />
                        @else
                            <x-heroicon-s-check-circle class="h-6 w-6" />
                        @endif
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-950 dark:text-white">
                            {{ $isDown ? 'Site is in maintenance mode' : 'Site is live' }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $isDown ? 'Visitors are currently seeing the maintenance page.' : 'Your site is accessible to all visitors.' }}
                        </p>
                    </div>
                </div>

                <x-filament::badge :color="$isDown ? 'warning' : 'success'" size="lg">
                    {{ $isDown ? 'Offline' : 'Online' }}
                </x-filament::badge>
            </div>
        </x-filament::section>

        {{-- Bypass URL --}}
        @if ($isDown && $secret)
            <x-filament::section>
                <x-slot name="heading">Admin Bypass URL</x-slot>
                <x-slot name="description">
                    Visit this URL once to set a bypass cookie, allowing you to browse the site normally while maintenance mode is active.
                </x-slot>

                <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                    <code class="flex-1 break-all text-sm text-gray-700 dark:text-gray-300">
                        {{ url($secret) }}
                    </code>
                    <x-filament::icon-button
                        icon="heroicon-o-clipboard"
                        x-on:click="window.navigator.clipboard.writeText('{{ url($secret) }}'); $tooltip('Copied!')"
                        label="Copy URL"
                    />
                </div>
            </x-filament::section>
        @endif

        {{-- Info --}}
        <x-filament::section>
            <x-slot name="heading">How it works</x-slot>

            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <li class="flex items-start gap-2">
                    <x-heroicon-o-shield-check class="mt-0.5 h-4 w-4 shrink-0 text-primary-500" />
                    The admin panel at <code>/admin</code> is always accessible regardless of maintenance mode.
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-link class="mt-0.5 h-4 w-4 shrink-0 text-primary-500" />
                    Use the bypass URL above to browse the frontend while it is offline — visiting it once sets a cookie.
                </li>
                <li class="flex items-start gap-2">
                    <x-heroicon-o-arrow-path class="mt-0.5 h-4 w-4 shrink-0 text-primary-500" />
                    A new bypass URL is generated each time maintenance mode is enabled.
                </li>
            </ul>
        </x-filament::section>
    </div>
</x-filament-panels::page>