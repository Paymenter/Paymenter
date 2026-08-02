@props(['compact' => false])

@php
$socials = cyber_socials();
@endphp

@if(count($socials) > 0)
@if($compact)
<div class="flex flex-wrap items-center gap-2">
    @foreach($socials as $social)
    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
        title="{{ $social['label'] }}"
        class="size-10 flex items-center justify-center rounded-lg border border-neutral bg-background-secondary/60 hover:border-primary hover:text-primary transition">
        <x-dynamic-component :component="$social['icon']" class="size-5" />
        <span class="sr-only">{{ $social['label'] }}</span>
    </a>
    @endforeach
</div>
@else
<section class="container py-12">
    <x-cyber.section-title
        title="Únete a la comunidad"
        subtitle="Novedades, promociones y soporte en nuestras redes."
        icon="ri-broadcast-fill" />

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
        @foreach($socials as $social)
        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
            class="cyber-card cyber-card-hover cyber-clip-sm p-4 flex items-center gap-4 group">
            <div class="p-3 rounded-lg border border-neutral group-hover:border-primary/50 transition shrink-0"
                style="background: {{ $social['color'] }}1a">
                <x-dynamic-component :component="$social['icon']" class="size-6" style="color: {{ $social['color'] }}" />
            </div>
            <div class="min-w-0">
                <p class="font-bold truncate">{{ $social['label'] }}</p>
                <p class="text-xs text-base/55">Seguir</p>
            </div>
            <x-ri-external-link-line class="size-4 ml-auto text-base/30 group-hover:text-primary transition shrink-0" />
        </a>
        @endforeach
    </div>
</section>
@endif
@endif
