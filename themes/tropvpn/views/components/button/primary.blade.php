@props([])

<button
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold',
        'bg-gradient-to-r from-primary to-secondary text-inverted',
        'shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:opacity-90',
        'transition-all duration-200 active:scale-[0.98]',
        'disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none',
    ]) }}
>
    {{ $slot }}
</button>
