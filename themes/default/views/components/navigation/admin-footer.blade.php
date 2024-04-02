<footer class="flex flex-col md:flex-row md:justify-between md:items-center p-2 px-4 bg-primary-200 dark:bg-primary-800">
    <div class="flex flex-col md:flex-row md:gap-4">
        <p class="text-sm text-primary-500 dark:text-primary-400">
            Powered by <a href="https://paymenter.org" target="_blank"
                class="text-primary-500 dark:text-primary-400">Paymenter &copy; {{ date('Y') }}</a>
        </p>
        <!-- Sponsor link -->
        <p class="text-sm text-primary-500 dark:text-primary-400">
            <a href="https://github.com/sponsors/Paymenter" target="_blank"
                class="text-primary-500 dark:text-primary-400">{{ __('Sponsor') }} <span class="animate-pulse">❤️</span></a>
        </p>
    </div>
    <div class="flex flex-col md:flex-row md:gap-2">
        <p class="text-sm text-primary-500 dark:text-primary-400">
            {{ __('Load time') }}: {{ round(microtime(true) - LARAVEL_START, 3) }}s
        </p>
        <p class="text-sm text-primary-500 dark:text-primary-400">
            {{ __('Version') }}: {{ config('settings.version', 'development') }}
        </p>
    </div>
</footer>