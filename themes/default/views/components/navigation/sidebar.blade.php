<aside 
    id="main-aside" 
    class="mt-14 w-64 h-screen md:flex hidden flex-col justify-between border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/95 fixed top-0 left-0 rtl:right-0 z-10 shadow-sm"
>
    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 hover:scrollbar-thumb-gray-400 dark:hover:scrollbar-thumb-gray-600">
        <x-navigation.sidebar-links />
    </div>
    
    {{-- Optional: Footer section for sidebar (e.g., version info, additional links) --}}
    @if(isset($footer) || true)
    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
        <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
            <span>v{{ config('app.version', '1.0.0') }}</span>
        </div>
    </div>
    @endif
</aside>