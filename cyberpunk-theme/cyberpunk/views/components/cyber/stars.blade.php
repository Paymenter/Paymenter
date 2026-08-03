@props([
    'value' => 0,      // nota de 0 a 5 (admite decimales: 4.5)
    'size' => 'size-5',
    'showValue' => false,
])

@php
$valor = max(0, min(5, (float) $value));
@endphp

<span {{ $attributes->merge(['class' => 'cyber-stars inline-flex items-center gap-0.5 align-middle']) }}
    role="img" aria-label="{{ number_format($valor, 1, ',', '.') }} de 5 estrellas">
    @for($i = 1; $i <= 5; $i++)
    @php
        // Cuánto de esta estrella hay que pintar: 0, medio o entero.
        $relleno = max(0, min(1, $valor - ($i - 1)));
    @endphp
    <span class="relative inline-block {{ $size }} shrink-0">
        <x-ri-star-fill class="absolute inset-0 {{ $size }} text-base/15" />
        @if($relleno > 0)
        <span class="absolute inset-0 overflow-hidden" style="width: {{ round($relleno * 100) }}%">
            <x-ri-star-fill class="{{ $size }} cyber-star-on" />
        </span>
        @endif
    </span>
    @endfor

    @if($showValue)
    <span class="ms-1.5 font-bold text-base/85">{{ number_format($valor, 1, ',', '.') }}</span>
    @endif
</span>
