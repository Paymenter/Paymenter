<div x-data="{ open: false }">
    <div x-anchor.offset.3="$refs.trigger" x-show="open"
        class="absolute top-0 left-0 text-base text-sm w-max p-[5px] rounded bg-background shadow-lg z-10 border border-neutral"
        aria-describedby="tooltip">
        {{ $message }}
        <div
            class="arrow absolute w-2 h-2 rotate-45 bg-background border-l border-t border-neutral -top-1 left-1/2 -translate-x-1/2">
        </div>
    </div>
    <div aria-describedby="tooltip" class="underline decoration-dotted " x-ref="trigger" @mouseover="open = true"
        @mouseout="open = false">
        {{ $slot }}
    </div>
</div>