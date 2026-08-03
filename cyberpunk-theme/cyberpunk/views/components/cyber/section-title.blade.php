@props(['title' => '', 'subtitle' => null, 'icon' => null, 'action' => null, 'actionUrl' => null, 'align' => null])

@php
$a = $align ?? cyber_align();
@endphp

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    {{-- flex-1 es lo que le da ancho al bloque: sin él, mx-auto y
         justify-center no tienen espacio donde centrar. --}}
    <div class="flex-1 min-w-0 {{ $a['text'] }}">
        <div class="flex flex-wrap items-center gap-3 {{ $a['items'] }}">
            @if($icon)
            <div class="p-2 rounded-lg bg-primary/10 border border-primary/25 shrink-0">
                <x-dynamic-component :component="$icon" class="size-5 text-primary" />
            </div>
            @endif
            <h2 class="text-2xl md:text-3xl font-black cyber-neon-text">
                <span class="cyber-glitch" data-text="{{ $title }}">{{ $title }}</span>
            </h2>
        </div>
        @if($subtitle)
        <p class="mt-3 text-base/65 max-w-2xl {{ $a['wrapper'] }}">{{ $subtitle }}</p>
        @endif
    </div>
    @if($action && $actionUrl)
    <a href="{{ $actionUrl }}" class="shrink-0 text-sm font-semibold text-primary flex items-center gap-1.5 hover:gap-2.5 transition-all">
        {{ $action }} <x-ri-arrow-right-line class="size-4" />
    </a>
    @endif
</div>
<div class="cyber-divider mt-5"></div>
