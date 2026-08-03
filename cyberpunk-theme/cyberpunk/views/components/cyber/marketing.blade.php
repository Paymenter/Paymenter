@php
$cards = cyber_marketing('marketing_cards');
$features = cyber_marketing('features');
$title = cyber_cfg('marketing_title', '¿Qué puedes montar con nosotros?');
$align = cyber_align();
$subtitle = cyber_cfg('marketing_subtitle', 'Infraestructura preparada para tus webs, bots y aplicaciones. Elige tu plan y despliega en minutos.');
@endphp

@if(count($cards) > 0)
<section class="container py-12">
    <x-cyber.section-title :title="$title" :subtitle="$subtitle" icon="ri-rocket-2-fill" />

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
        @foreach($cards as $card)
        @php $url = $card['url'] ?? ''; @endphp
        <{{ $url ? 'a' : 'div' }}
            @if($url) href="{{ $url }}" @endif
            class="cyber-card cyber-card-hover cyber-clip p-5 flex flex-col group">
            <div class="p-3 rounded-xl bg-primary/10 border border-primary/25 w-fit group-hover:bg-primary/20 transition">
                <x-dynamic-component :component="$card['icon'] ?? 'ri-server-fill'" class="size-6 text-primary" />
            </div>
            <h3 class="mt-4 text-lg font-bold">{{ $card['title'] ?? '' }}</h3>
            @if(!empty($card['description']))
            <p class="mt-2 text-sm text-base/65 leading-relaxed">{{ $card['description'] }}</p>
            @endif
            @if($url)
            <span class="mt-auto pt-4 text-sm font-semibold text-primary flex items-center gap-1.5">
                Ver más <x-ri-arrow-right-line class="size-4" />
            </span>
            @endif
        </{{ $url ? 'a' : 'div' }}>
        @endforeach
    </div>

    @if(count($features) > 0)
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
        @foreach($features as $feature)
        <div class="flex items-start gap-3 rounded-xl border border-neutral bg-background-secondary/50 p-4">
            <x-dynamic-component :component="$feature['icon'] ?? 'ri-check-double-fill'" class="size-5 text-accent shrink-0 mt-0.5" />
            <div>
                <p class="font-semibold text-sm">{{ $feature['title'] ?? '' }}</p>
                @if(!empty($feature['description']))
                <p class="text-xs text-base/60 mt-1">{{ $feature['description'] }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>
@endif
