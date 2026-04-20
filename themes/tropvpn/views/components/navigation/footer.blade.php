<footer class="w-full border-t border-neutral/30 mt-auto">
    <div class="container mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2.5">
            <div class="h-6 w-6 rounded-lg overflow-hidden">
                <x-logo class="h-full w-full object-cover" />
            </div>
            <span class="text-sm font-semibold" style="font-family: 'Space Grotesk', sans-serif;">
                {{ config('app.name') }}
            </span>
        </div>

        <p class="text-xs text-muted text-center">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>

        <div class="flex items-center gap-4">
            <a href="https://tropvpn.com/privacy" target="_blank"
               class="text-xs text-muted hover:text-base transition-colors">
                Privacy
            </a>
            <a href="https://tropvpn.com/terms" target="_blank"
               class="text-xs text-muted hover:text-base transition-colors">
                Terms
            </a>
            <a href="https://tropvpn.com/contact" target="_blank"
               class="text-xs text-muted hover:text-base transition-colors">
                Support
            </a>
        </div>
    </div>
</footer>
