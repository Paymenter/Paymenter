@props([
    'comment',
    'likedComments' => [],
    'replyingTo' => null,
    'replyBody' => [],
    'canModerate' => false,
    'bare' => false,        // true = sin caja propia (ya viene envuelto)
])

@php
$autor = $comment->user?->name ?? 'Usuario';
$esPropia = auth()->check() && auth()->id() === $comment->user_id;
$puedeBorrar = $esPropia || $canModerate;
@endphp

<div class="{{ $bare ? '' : 'rounded-xl border border-neutral bg-background/40 p-4' }}">
    <div class="flex gap-3">
        <img src="{{ cyber_avatar($comment->user) }}" alt="avatar"
            class="size-10 rounded-full border border-neutral object-cover shrink-0">

        <div class="flex-grow min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span class="font-bold text-sm truncate">{{ $autor }}</span>
                        @if($esPropia)
                        <span class="rounded-md bg-primary/15 text-primary px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">Tú</span>
                        @endif
                        @if($comment->featured)
                        <span class="inline-flex items-center gap-1 rounded-md bg-accent/15 text-accent px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                            <x-ri-award-fill class="size-3" /> Destacada
                        </span>
                        @endif
                        <span class="text-xs text-base/45">· {{ $comment->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Las estrellas que puso este usuario --}}
                    @if($comment->rating)
                    <div class="mt-1.5 flex items-center gap-2">
                        <x-cyber.stars :value="$comment->rating" size="size-4" />
                        <span class="text-xs font-bold text-base/70">{{ $comment->rating }}/5</span>
                    </div>
                    @endif
                </div>

                @if($puedeBorrar)
                <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="¿Eliminar esta reseña?"
                    class="text-base/30 hover:text-error transition cursor-pointer shrink-0" title="Eliminar">
                    <x-ri-delete-bin-6-line class="size-4" />
                </button>
                @endif
            </div>

            <p class="mt-2 text-sm text-base/80 whitespace-pre-line break-words leading-relaxed">
                {!! cyber_linkify($comment->content) !!}
            </p>

            <div class="mt-3 flex items-center gap-4 text-xs">
                <button wire:click="toggleCommentLike({{ $comment->id }})"
                    class="inline-flex items-center gap-1.5 font-semibold transition cursor-pointer
                    {{ in_array($comment->id, $likedComments, true) ? 'text-primary' : 'text-base/50 hover:text-primary' }}">
                    <x-ri-thumb-up-fill class="size-3.5" />
                    Útil @if($comment->likes_count > 0) ({{ $comment->likes_count }}) @endif
                </button>
                @auth
                <button wire:click="$set('replyingTo', {{ $comment->id }})"
                    class="inline-flex items-center gap-1 text-base/50 hover:text-accent font-semibold cursor-pointer">
                    <x-ri-reply-fill class="size-3.5" /> Responder
                </button>
                @endauth
            </div>

            @if($replyingTo === $comment->id)
            <div class="mt-3">
                <p class="text-xs text-accent font-semibold mb-1.5 flex items-center gap-1">
                    <x-ri-corner-down-right-line class="size-3.5" />
                    Respondiendo a {{ $autor }}
                </p>
                <div class="flex gap-2">
                    <input type="text" wire:model="replyBody.{{ $comment->id }}"
                        wire:keydown.enter="reply({{ $comment->id }})"
                        placeholder="Tu respuesta..."
                        class="flex-grow rounded-lg border border-accent/40 bg-background/60 text-base px-3 py-2 text-sm focus:border-accent focus:ring-0">
                    <button wire:click="reply({{ $comment->id }})"
                        class="px-3 rounded-lg border border-accent/40 bg-accent/10 text-accent hover:bg-accent/20 transition cursor-pointer">
                        <x-ri-send-plane-fill class="size-4" />
                    </button>
                    <button wire:click="$set('replyingTo', null)"
                        class="px-2 rounded-lg border border-neutral text-base/50 hover:text-error transition cursor-pointer">
                        <x-ri-close-fill class="size-4" />
                    </button>
                </div>
            </div>
            @endif

            {{-- Respuestas --}}
            @if($comment->replies->count() > 0)
            <div class="mt-4 space-y-3 ps-4 border-s-2 border-accent/30">
                @foreach($comment->replies as $reply)
                @if($reply->approved)
                <div class="flex gap-2">
                    <img src="{{ cyber_avatar($reply->user) }}" alt="avatar"
                        class="size-7 rounded-full border border-neutral object-cover shrink-0">
                    <div class="flex-grow min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-x-2 min-w-0">
                                <span class="font-semibold text-sm truncate">{{ $reply->user?->name ?? 'Usuario' }}</span>
                                <span class="inline-flex items-center gap-1 text-[11px] text-accent font-semibold">
                                    <x-ri-corner-down-right-line class="size-3" />
                                    en respuesta a {{ $autor }}
                                </span>
                                <span class="text-xs text-base/40">· {{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            @if(auth()->check() && (auth()->id() === $reply->user_id || $canModerate))
                            <button wire:click="deleteComment({{ $reply->id }})" wire:confirm="¿Eliminar respuesta?"
                                class="text-base/30 hover:text-error transition cursor-pointer shrink-0">
                                <x-ri-delete-bin-6-line class="size-3.5" />
                            </button>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-base/75 whitespace-pre-line break-words">
                            {!! cyber_linkify($reply->content) !!}
                        </p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
