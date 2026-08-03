@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;

$avatarOf = fn ($user) => $user ? (Avatars::url($user) ?? $user->avatar) : 'https://www.gravatar.com/avatar/?d=mp';
$canModerate = auth()->check() && auth()->user()->role_id !== null;
@endphp

<div class="cyber-card cyber-clip p-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-primary/10 border border-primary/25">
                <x-ri-chat-quote-fill class="size-5 text-primary" />
            </div>
            <div>
                <h2 class="text-xl font-black">Opiniones del plan</h2>
                <p class="text-sm text-base/55">{{ $stats['likes'] }} me gusta · {{ $totalComments }} comentarios</p>
            </div>
        </div>

        <button wire:click="toggleLike"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-bold text-sm transition cursor-pointer
            {{ $liked ? 'bg-primary text-white shadow-[0_0_18px_-6px_hsl(var(--color-primary))]' : 'border border-neutral hover:border-primary/60 hover:text-primary' }}">
            <x-ri-heart-3-fill class="size-5" />
            {{ $liked ? 'Te gusta' : 'Me gusta' }}
            <span class="opacity-70">({{ $stats['likes'] }})</span>
        </button>
    </div>

    <div class="cyber-divider my-6"></div>

    @auth
    <div class="flex gap-3">
        <img src="{{ $avatarOf(auth()->user()) }}" alt="avatar" class="size-10 rounded-full border border-neutral object-cover shrink-0">
        <div class="flex-grow">
            <textarea wire:model="body" rows="2" maxlength="1500"
                placeholder="Cuéntanos qué te parece este plan..."
                class="w-full rounded-lg border border-neutral bg-background/60 px-4 py-2.5 text-sm focus:border-primary focus:ring-0"></textarea>
            <div class="mt-2 flex justify-end">
                <x-button.primary wire:click="comment" wire:loading.attr="disabled" class="!w-fit">
                    <x-ri-send-plane-fill class="size-4" />
                    Comentar
                </x-button.primary>
            </div>
        </div>
    </div>
    @else
    <div class="rounded-xl border border-neutral bg-background/40 p-4 text-center text-sm text-base/65">
        <a href="{{ route('login') }}" wire:navigate class="text-primary font-semibold hover:underline">Inicia sesión</a>
        para dar like y comentar en este plan.
    </div>
    @endauth

    <div class="mt-6 space-y-4">
        @forelse($comments as $comment)
        <div class="rounded-xl border border-neutral bg-background/40 p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="{{ $avatarOf($comment->user) }}" alt="avatar" class="size-9 rounded-full border border-neutral object-cover">
                    <div>
                        <p class="font-semibold text-sm">{{ $comment->user?->name ?? 'Usuario' }}</p>
                        <p class="text-xs text-base/45">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if(auth()->check() && (auth()->id() === $comment->user_id || $canModerate))
                <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="¿Eliminar comentario?"
                    class="text-base/35 hover:text-error transition cursor-pointer">
                    <x-ri-delete-bin-6-line class="size-4" />
                </button>
                @endif
            </div>

            <p class="mt-2 text-sm text-base/80 whitespace-pre-line break-words">{!! \Paymenter\Extensions\Others\CyberpunkTheme\Support\Text::linkify($comment->content) !!}</p>

            <div class="mt-3 flex items-center gap-4 text-xs">
                <button wire:click="toggleCommentLike({{ $comment->id }})"
                    class="inline-flex items-center gap-1.5 font-semibold text-base/50 hover:text-primary transition cursor-pointer">
                    <x-ri-heart-3-fill class="size-3.5" /> {{ $comment->likes_count }}
                </button>
                @auth
                <button wire:click="$set('replyingTo', {{ $comment->id }})"
                    class="text-base/50 hover:text-accent font-semibold cursor-pointer">Responder</button>
                @endauth
            </div>

            @if($replyingTo === $comment->id)
            <div class="mt-3 flex gap-2">
                <input type="text" wire:model="replyBody.{{ $comment->id }}"
                    wire:keydown.enter="reply({{ $comment->id }})"
                    placeholder="Tu respuesta..."
                    class="flex-grow rounded-lg border border-neutral bg-background/60 px-3 py-1.5 text-sm focus:border-accent focus:ring-0">
                <button wire:click="reply({{ $comment->id }})"
                    class="px-3 rounded-lg border border-accent/40 bg-accent/10 text-accent hover:bg-accent/20 transition cursor-pointer">
                    <x-ri-send-plane-fill class="size-4" />
                </button>
            </div>
            @endif

            @if($comment->replies->count() > 0)
            <div class="mt-4 space-y-3 ps-5 border-s border-neutral">
                @foreach($comment->replies as $reply)
                @if($reply->approved)
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $avatarOf($reply->user) }}" alt="avatar" class="size-7 rounded-full border border-neutral object-cover">
                            <p class="font-semibold text-sm">{{ $reply->user?->name ?? 'Usuario' }}</p>
                            <span class="text-xs text-base/40">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        @if(auth()->check() && (auth()->id() === $reply->user_id || $canModerate))
                        <button wire:click="deleteComment({{ $reply->id }})" wire:confirm="¿Eliminar respuesta?"
                            class="text-base/35 hover:text-error transition cursor-pointer">
                            <x-ri-delete-bin-6-line class="size-3.5" />
                        </button>
                        @endif
                    </div>
                    <p class="mt-1.5 text-sm text-base/75 whitespace-pre-line break-words">{!! \Paymenter\Extensions\Others\CyberpunkTheme\Support\Text::linkify($reply->content) !!}</p>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <p class="text-sm text-base/50 text-center py-6">Todavía no hay comentarios en este plan.</p>
        @endforelse
    </div>

    @if(!$showAll && $totalComments > 5)
    <button wire:click="$set('showAll', true)"
        class="mt-5 w-full py-2.5 rounded-lg border border-neutral text-sm font-semibold hover:border-primary/60 hover:text-primary transition cursor-pointer">
        Ver los {{ $totalComments }} comentarios
    </button>
    @endif
</div>
