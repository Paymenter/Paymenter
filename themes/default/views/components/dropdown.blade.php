<div class="relative" x-data="{ open: false }">

    <button class="flex flex-row items-center px-2 py-1"
        x-on:click="open = !open">
        {{ $trigger }}
        <svg x-bind:class="{ '-rotate-180' : open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 ml-2 text-base ease-out duration-300" fill="currentColor">
            <path d="M11.9999 13.1714L16.9497 8.22168L18.3639 9.63589L11.9999 15.9999L5.63599 9.63589L7.0502 8.22168L11.9999 13.1714Z"></path>
        </svg>
    </button>

    <div class="absolute right-0 mt-2 w-48 p-3 bg-background-secondary rounded-md shadow-lg z-10 border border-neutral"
        x-show="open" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" x-on:click.outside="open = false" x-cloak>
        {{ $content }}
    </div>
</div>
