@php
// Sólo tiene sentido con la extensión activa: las reseñas viven en sus tablas.
$reviews = collect();
$general = ['count' => 0, 'average' => 0];

if (cyber_ext()) {
    try {
        $reviews = \Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews::featured();
        $general = \Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews::generalStats();
    } catch (\Throwable $e) {
        $reviews = collect();
    }
}

$align = cyber_align();
$reviewsUrl = cyber_ext()
    ? url('/' . \Paymenter\Extensions\Others\CyberpunkTheme\Support\Config::reviewsSlug())
    : null;
@endphp

@if($reviews->count() > 0)
<section class="container py-12">
    <x-cyber.section-title
        :title="cyber_cfg('featured_reviews_title', 'Lo que dicen nuestros clientes')"
        :subtitle="cyber_cfg('featured_reviews_subtitle', 'Opiniones reales de gente que ya tiene sus servicios con nosotros.')"
        icon="ri-star-smile-fill"
        :action="$reviewsUrl ? 'Ver todas' : null"
        :actionUrl="$reviewsUrl" />

    {{-- Nota media del servicio --}}
    @if(($general['count'] ?? 0) > 0)
    <div class="mt-8 flex justify-center">
        <div class="inline-flex flex-wrap items-center justify-center gap-x-4 gap-y-2 rounded-2xl border border-neutral bg-background-secondary/60 px-6 py-4">
            <span class="text-4xl font-black cyber-gradient-text leading-none">
                {{ number_format((float) $general['average'], 1, ',', '.') }}
            </span>
            <div class="text-start">
                <x-cyber.stars :value="$general['average']" size="size-5" />
                <p class="text-xs text-base/55 mt-1">
                    {{ $general['count'] }} {{ $general['count'] === 1 ? 'opinión' : 'opiniones' }} sobre el servicio
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid gap-5 mt-8 {{ cyber_cols($reviews->count(), 3) }}">
        @foreach($reviews as $review)
        <article class="cyber-review-card p-5 flex flex-col">
            <span class="cyber-review-quote" aria-hidden="true">&rdquo;</span>

            <x-cyber.stars :value="$review->rating" size="size-5" />

            <p class="mt-3 text-base/80 leading-relaxed flex-grow">
                {{ Str::limit($review->content, 260) }}
            </p>

            <div class="mt-5 pt-4 border-t border-neutral flex items-center gap-3">
                <img src="{{ cyber_avatar($review->user) }}" alt="avatar"
                    class="size-10 rounded-full border border-primary/40 object-cover shrink-0">
                <div class="min-w-0">
                    <p class="font-bold text-sm truncate">{{ $review->user?->name ?? 'Cliente' }}</p>
                    <p class="text-xs text-base/50 truncate">
                        {{ $review->targetLabel() }} · {{ $review->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    @if($reviewsUrl)
    <div class="mt-8 flex {{ $align['items'] }}">
        <a href="{{ $reviewsUrl }}" class="w-fit">
            <x-button.secondary class="!w-fit px-6 py-2.5">
                <x-ri-star-smile-fill class="size-4" />
                Ver todas las reseñas
            </x-button.secondary>
        </a>
    </div>
    @endif
</section>
@endif
