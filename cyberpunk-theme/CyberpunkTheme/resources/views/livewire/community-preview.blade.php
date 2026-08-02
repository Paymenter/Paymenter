@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;

$avatarOf = fn ($user) => $user ? (Avatars::url($user) ?? $user->avatar) : 'https://www.gravatar.com/avatar/?d=mp';
@endphp

<section class="container py-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-primary/10 border border-primary/25">
                    <x-ri-chat-smile-2-fill class="size-5 text-primary" />
                </div>
                <h2 class="text-2xl md:text-3xl font-black cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ $communityName }}">{{ $communityName }}</span>
                </h2>
            </div>
            <p class="mt-3 text-base/65 max-w-2xl">{{ $description }}</p>
        </div>
        <a href="{{ $communityUrl }}" class="text-sm font-semibold text-primary flex items-center gap-1.5 hover:gap-2.5 transition-all">
            Ver todo <x-ri-arrow-right-line class="size-4" />
        </a>
    </div>
    <div class="cyber-divider mt-5"></div>

    @if($posts->count() > 0)
    <div class="grid md:grid-cols-3 gap-4 mt-8">
        @foreach($posts as $post)
        <a href="{{ $communityUrl }}" class="cyber-card cyber-card-hover cyber-clip overflow-hidden flex flex-col">
            @if($post->media->count() > 0)
            @php $first = $post->media->first(); @endphp
            <div class="h-40 cyber-media bg-background/50 relative">
                @if($first->isVideo())
                <div class="w-full h-full flex items-center justify-center">
                    <x-ri-play-circle-fill class="size-12 text-primary" />
                </div>
                @else
                <img src="{{ $first->url }}" alt="" loading="lazy" class="w-full h-full object-cover">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-background-secondary to-transparent"></div>
            </div>
            @endif
            <div class="p-5 flex flex-col flex-grow">
                <div class="flex items-center gap-2">
                    <img src="{{ $avatarOf($post->user) }}" alt="avatar" class="size-8 rounded-full border border-neutral object-cover">
                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate">{{ $post->user?->name ?? 'Usuario' }}</p>
                        <p class="text-xs text-base/45">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if($post->title)
                <h3 class="mt-3 font-bold">{{ $post->title }}</h3>
                @endif
                <p class="mt-2 text-sm text-base/65 line-clamp-3">{{ $post->content }}</p>
                <div class="mt-auto pt-4 flex items-center gap-4 text-xs text-base/55">
                    <span class="inline-flex items-center gap-1.5"><x-ri-heart-3-fill class="size-3.5 text-primary" /> {{ $post->likes_count }}</span>
                    <span class="inline-flex items-center gap-1.5"><x-ri-chat-3-fill class="size-3.5 text-accent" /> {{ $post->comments_count }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="cyber-card cyber-clip p-8 mt-8 text-center">
        <x-ri-chat-smile-2-line class="size-10 mx-auto text-base/25" />
        <p class="mt-3 text-base/60">Aún no hay publicaciones en la comunidad.</p>
        <a href="{{ $communityUrl }}" class="inline-block mt-4">
            <x-button.primary class="!w-fit px-6">Entrar a la comunidad</x-button.primary>
        </a>
    </div>
    @endif
</section>
