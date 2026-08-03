@props([
    'stats' => ['count' => 0, 'average' => 0, 'distribution' => []],
    'compact' => false,
])

@php
$total = (int) ($stats['count'] ?? 0);
$media = (float) ($stats['average'] ?? 0);
$dist = $stats['distribution'] ?? [];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row gap-6 sm:items-center']) }}>
    {{-- Nota media --}}
    <div class="text-center shrink-0">
        <div class="text-5xl font-black cyber-gradient-text leading-none">
            {{ $total > 0 ? number_format($media, 1, ',', '.') : '—' }}
        </div>
        <x-cyber.stars :value="$media" size="size-5" class="mt-2 justify-center" />
        <p class="mt-2 text-xs text-base/55">
            {{ $total }} {{ $total === 1 ? 'reseña' : 'reseñas' }}
        </p>
    </div>

    @if(!$compact)
    {{-- Reparto de estrellas --}}
    <div class="flex-grow min-w-0 space-y-1.5">
        @for($estrella = 5; $estrella >= 1; $estrella--)
        @php
            $n = (int) ($dist[$estrella] ?? 0);
            $pct = $total > 0 ? round($n * 100 / $total) : 0;
        @endphp
        <div class="flex items-center gap-2.5 text-xs">
            <span class="w-10 shrink-0 text-base/55 tabular-nums">{{ $estrella }} ★</span>
            <span class="cyber-meter flex-grow">
                <span style="width: {{ $pct }}%"></span>
            </span>
            <span class="w-8 shrink-0 text-end text-base/55 tabular-nums">{{ $n }}</span>
        </div>
        @endfor
    </div>
    @endif
</div>
