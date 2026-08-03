@props(['label' => 'MÁS POPULAR', 'position' => 'top-right'])

@php
$pos = match ($position) {
    'top-left' => 'top-0 left-0 rounded-br-xl rounded-tl-xl',
    default => 'top-0 right-0 rounded-bl-xl rounded-tr-xl',
};
@endphp

{{-- Etiqueta bien visible: cinta con degradado, borde y brillo --}}
<div class="absolute {{ $pos }} z-20 pointer-events-none">
    <div class="cyber-popular flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-black uppercase tracking-wider text-white">
        <x-ri-fire-fill class="size-4 shrink-0" />
        {{ $label }}
    </div>
</div>
