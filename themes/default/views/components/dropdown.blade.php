<div class="relative" x-data="{ open: false }">

    <button class="flex flex-row items-center px-2 py-1 text-sm font-semibold whitespace-nowrap text-base hover:text-base/80"
        x-on:click="open = !open">
        {{ $trigger }}
        <x-ri-arrow-down-s-line x-bind:class="{ '-rotate-180' : open }" class="md:block hidden size-4 text-base ease-out duration-300" />
    </button>

    <div class="absolute left-0 md:left-auto md:right-0 mt-2 w-48 px-2 py-1 bg-background-secondary rounded-md shadow-lg z-10 border border-neutral"
        x-show="open" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" x-on:click.outside="open = false" x-cloak>
        {{ $content }}
    </div>
</div>
