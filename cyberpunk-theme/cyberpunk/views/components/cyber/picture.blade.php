@props([
    'src' => null,
    'alt' => '',
    'height' => 'h-40',
    'max' => 'max-h-[24rem]',
    'gradient' => true,
    'rounded' => '',
    'mode' => null,
])

@php
// full  → la imagen se ve entera y la caja se adapta a su tamaño
// cover → la imagen se recorta a una altura fija
$imageMode = $mode ?? cyber_image_mode();
$isFull = $imageMode === 'full';
@endphp

@if($src)
<div class="relative w-full overflow-hidden bg-background/40 {{ $rounded }} {{ $isFull ? '' : $height }}">
    @if($isFull)
    {{-- Copia desenfocada detrás: si la imagen es muy alta y hay que limitarla,
         los lados se rellenan con ella misma en vez de quedar en negro. --}}
    <img src="{{ $src }}" alt="" aria-hidden="true" loading="lazy"
        class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-40">
    @endif

    <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy"
        class="relative {{ $isFull ? 'block w-full h-auto ' . $max . ' object-contain object-center mx-auto' : 'w-full h-full object-cover object-center' }}">

    @if($gradient)
    {{-- Degradado inferior: mantiene legible el texto que va debajo o encima --}}
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-background-secondary via-background-secondary/45 to-transparent"></div>
    @endif
</div>
@endif
