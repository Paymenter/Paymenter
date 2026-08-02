@php
$links = cyber_quick_links();
@endphp

@if(count($links) > 0)
<section class="container py-8">
    <x-cyber.section-title
        title="Accesos rápidos"
        subtitle="Entra directo a la tienda o a la sección que necesites."
        icon="ri-flashlight-fill" />

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-8">
        @foreach($links as $link)
        <a href="{{ $link['url'] }}"
            @if(!empty($link['target'])) target="{{ $link['target'] }}" rel="noopener noreferrer" @endif
            class="cyber-card cyber-card-hover cyber-clip-sm p-4 flex items-center gap-4 group">
            <div class="p-3 rounded-lg bg-accent/10 border border-accent/25 group-hover:bg-accent/20 transition shrink-0">
                <x-dynamic-component :component="$link['icon']" class="size-5 text-accent" />
            </div>
            <div class="min-w-0">
                <p class="font-bold truncate">{{ $link['label'] }}</p>
                @if(!empty($link['description']))
                <p class="text-xs text-base/60 truncate">{{ $link['description'] }}</p>
                @endif
            </div>
            <x-ri-arrow-right-line class="size-4 ml-auto text-base/30 group-hover:text-primary transition shrink-0" />
        </a>
        @endforeach
    </div>
</section>
@endif
