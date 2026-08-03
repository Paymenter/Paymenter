@props([
    'model' => 'rating',   // propiedad Livewire donde se guarda la nota
    'value' => 0,
    'size' => 'size-8',
])

@php
$etiquetas = [
    1 => 'Muy malo',
    2 => 'Malo',
    3 => 'Normal',
    4 => 'Bueno',
    5 => 'Excelente',
];
@endphp

{{-- Selector de estrellas. La nota se guarda en la propiedad Livewire
     indicada en :model, así que el botón de publicar puede exigirla. --}}
<div class="flex flex-wrap items-center gap-3"
    x-data="{
        nota: @entangle($model),
        encima: 0,
        etiquetas: {{ Js::from($etiquetas) }},
        get mostrada() { return this.encima || this.nota || 0 },
    }"
    x-on:mouseleave="encima = 0">

    <div class="flex items-center gap-1">
        @for($i = 1; $i <= 5; $i++)
        <button type="button"
            x-on:click="nota = (nota === {{ $i }} ? 0 : {{ $i }})"
            x-on:mouseenter="encima = {{ $i }}"
            :aria-pressed="nota >= {{ $i }}"
            aria-label="{{ $i }} {{ $i === 1 ? 'estrella' : 'estrellas' }}"
            class="cursor-pointer transition-transform hover:scale-110 focus:outline-none">
            <x-ri-star-fill class="{{ $size }}"
                ::class="mostrada >= {{ $i }} ? 'cyber-star-on' : 'text-base/20'" />
        </button>
        @endfor
    </div>

    <span class="text-sm font-semibold"
        :class="mostrada > 0 ? 'text-primary' : 'text-base/40'"
        x-text="mostrada > 0 ? etiquetas[mostrada] : 'Elige tu puntuación'"></span>
</div>
