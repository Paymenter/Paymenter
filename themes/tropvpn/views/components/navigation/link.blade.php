@props([
    'href' => '#',
    'spa'  => true,
])

@php
    $isActive = request()->url() === $href || request()->is(ltrim(parse_url($href, PHP_URL_PATH), '/'));
@endphp

<a
    href="{{ $href }}"
    @if($spa) wire:navigate @endif
    {{ $attributes->class([
        'flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition-all',
        'text-muted hover:text-base hover:bg-neutral/30'    => !$isActive,
        'text-primary bg-primary/10 border border-primary/20' => $isActive,
    ]) }}
>
    {{ $slot }}
</a>
