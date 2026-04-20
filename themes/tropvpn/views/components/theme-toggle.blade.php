{{-- Theme toggle: cycles dark → light → system --}}
<button
    @click="theme = theme === 'dark' ? 'light' : theme === 'light' ? 'system' : 'dark'"
    class="p-2 rounded-xl hover:bg-neutral/30 transition-colors text-muted hover:text-base"
    :aria-label="'Switch to ' + (theme === 'dark' ? 'light' : theme === 'light' ? 'system' : 'dark') + ' mode'"
>
    {{-- Sun (light) --}}
    <svg x-show="!isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
    </svg>
    {{-- Moon (dark) --}}
    <svg x-show="isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
    </svg>
</button>
