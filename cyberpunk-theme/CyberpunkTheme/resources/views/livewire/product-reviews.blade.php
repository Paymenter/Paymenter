@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews as ReviewsHelper;

$canModerate = auth()->check() && auth()->user()->role_id !== null;
@endphp

<div class="cyber-card cyber-clip p-6 md:p-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-primary/10 border border-primary/25">
                <x-ri-star-smile-fill class="size-6 text-primary" />
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-black">Opiniones de este plan</h2>
                <p class="text-sm text-base/55">{{ ReviewsHelper::summaryLabel($stats) }}</p>
            </div>
        </div>

        @if($stats['count'] > 0)
        <div class="flex items-center gap-3">
            <x-cyber.stars :value="$stats['average']" size="size-6" />
            <span class="text-3xl font-black cyber-gradient-text leading-none">
                {{ number_format($stats['average'], 1, ',', '.') }}
            </span>
        </div>
        @endif
    </div>

    @if($stats['count'] > 0)
    <div class="cyber-divider my-6"></div>
    <x-cyber.rating-summary :stats="$stats" />
    @endif

    <div class="cyber-divider my-6"></div>

    <x-cyber.review-form
        :rating="$rating"
        model="rating"
        body="body"
        :value="$body"
        action="publishReview"
        :own="$ownReview"
        placeholder="¿Qué tal te ha ido con este plan? Cuenta tu experiencia..." />

    <div class="mt-6 space-y-3">
        @forelse($comments as $comment)
        <x-cyber.review-item :comment="$comment" :likedComments="$likedComments"
            :replyingTo="$replyingTo" :replyBody="$replyBody" :canModerate="$canModerate" />
        @empty
        <div class="rounded-xl border border-dashed border-neutral p-8 text-center">
            <x-ri-star-line class="size-10 mx-auto text-base/20" />
            <p class="mt-3 text-sm text-base/50">Todavía nadie ha valorado este plan. ¡Sé el primero!</p>
        </div>
        @endforelse
    </div>

    @if(!$showAll && $stats['count'] > $comments->count())
    <div class="mt-5 text-center">
        <button wire:click="$set('showAll', true)"
            class="inline-flex items-center gap-2 rounded-lg border border-neutral px-5 py-2.5 text-sm font-semibold text-base/70 hover:text-primary hover:border-primary/50 transition cursor-pointer">
            <x-ri-arrow-down-s-line class="size-4" />
            Ver las {{ $stats['count'] }} reseñas
        </button>
    </div>
    @endif
</div>
