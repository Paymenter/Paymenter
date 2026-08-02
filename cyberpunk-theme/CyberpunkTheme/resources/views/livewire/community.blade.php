@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;

$avatarOf = fn ($user) => $user ? (Avatars::url($user) ?? $user->avatar) : 'https://www.gravatar.com/avatar/?d=mp';
$communityName = Config::theme('community_name', 'Comunidad');
$canModerate = auth()->check() && auth()->user()->role_id !== null;
@endphp

<div class="container mt-10 pb-16">
    {{-- Cabecera --}}
    <div class="cyber-card cyber-clip p-6 md:p-8 relative overflow-hidden">
        <div class="absolute inset-0 cyber-gradient opacity-[0.07] pointer-events-none"></div>
        <div class="relative flex flex-col md:flex-row md:items-end justify-between gap-5">
            <div>
                <span class="cyber-chip"><x-ri-chat-smile-2-fill class="size-3.5" /> {{ $communityName }}</span>
                <h1 class="mt-4 text-3xl md:text-4xl font-black cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ $communityName }}">{{ $communityName }}</span>
                </h1>
                <p class="mt-3 text-base/65 max-w-2xl">
                    {{ Config::theme('community_description', 'Comparte tu experiencia con el hosting: fotos, vídeos y opiniones.') }}
                </p>
            </div>

            <div class="flex gap-2">
                <select wire:model.live="sort"
                    class="rounded-lg border border-neutral bg-background/70 text-sm px-3 py-2 focus:border-primary focus:ring-0">
                    <option value="recent">Más recientes</option>
                    <option value="liked">Más gustadas</option>
                    <option value="commented">Más comentadas</option>
                </select>
            </div>
        </div>
    </div>

    @if(!empty($error))
    <div class="cyber-card cyber-clip p-5 mt-6 border-warning/50">
        <div class="flex items-start gap-3">
            <x-ri-error-warning-fill class="size-5 text-warning shrink-0" />
            <p class="text-sm text-base/75">{{ $error }}</p>
        </div>
    </div>
    @endif

    {{-- Crear publicación --}}
    @auth
    <div class="cyber-card cyber-clip p-6 mt-6">
        <div class="flex gap-4">
            <img src="{{ $avatarOf(auth()->user()) }}" alt="avatar"
                class="size-12 rounded-full border-2 border-primary/50 object-cover shrink-0">
            <div class="flex-grow">
                <input type="text" wire:model="title" maxlength="120"
                    placeholder="Título (opcional)"
                    class="w-full rounded-lg border border-neutral bg-background/60 px-4 py-2.5 text-sm mb-3 focus:border-primary focus:ring-0">
                @error('title') <p class="text-error text-xs mb-2">{{ $message }}</p> @enderror

                <textarea wire:model="content" rows="3" maxlength="2000"
                    placeholder="Cuenta tu experiencia con el hosting..."
                    class="w-full rounded-lg border border-neutral bg-background/60 px-4 py-3 text-sm focus:border-primary focus:ring-0"></textarea>
                @error('content') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-primary cursor-pointer hover:opacity-80">
                        <x-ri-image-add-fill class="size-5" />
                        Añadir fotos o vídeos
                        <input type="file" wire:model="media" multiple accept="image/*,video/mp4,video/webm" class="hidden">
                    </label>
                    <span class="text-xs text-base/45">Máx. {{ Config::theme('community_media_limit', 4) }} archivos · 20 MB c/u</span>
                    <div wire:loading wire:target="media" class="text-xs text-primary">Subiendo...</div>
                </div>
                @error('media.*') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

                @if(count($media) > 0)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($media as $file)
                    <div class="size-20 rounded-lg overflow-hidden border border-neutral bg-background/60 flex items-center justify-center">
                        @if(str_starts_with($file->getMimeType() ?? '', 'image'))
                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover" alt="">
                        @else
                        <x-ri-film-fill class="size-8 text-primary" />
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="mt-4 flex justify-end">
                    <x-button.primary wire:click="publish" wire:loading.attr="disabled" class="!w-fit">
                        <x-ri-send-plane-fill class="size-4" />
                        Publicar
                    </x-button.primary>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="cyber-card cyber-clip p-6 mt-6 text-center">
        <p class="text-base/70">Inicia sesión para publicar, dar like y comentar.</p>
        <a href="{{ route('login') }}" wire:navigate class="inline-block mt-4">
            <x-button.primary class="!w-fit px-6">Iniciar sesión</x-button.primary>
        </a>
    </div>
    @endauth

    {{-- Publicaciones --}}
    <div class="mt-6 space-y-5">
        @forelse($posts as $post)
        <article class="cyber-card cyber-clip p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ $avatarOf($post->user) }}" alt="avatar"
                        class="size-11 rounded-full border-2 border-primary/40 object-cover">
                    <div>
                        <p class="font-bold">{{ $post->user?->name ?? 'Usuario' }}</p>
                        <p class="text-xs text-base/50">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                    @if($post->pinned)
                    <span class="cyber-chip !py-0.5"><x-ri-pushpin-fill class="size-3" /> Destacado</span>
                    @endif
                </div>

                @if(auth()->check() && (auth()->id() === $post->user_id || $canModerate))
                <button wire:click="deletePost({{ $post->id }})"
                    wire:confirm="¿Eliminar esta publicación?"
                    class="text-base/40 hover:text-error transition cursor-pointer" title="Eliminar">
                    <x-ri-delete-bin-6-line class="size-5" />
                </button>
                @endif
            </div>

            @if($post->title)
            <h2 class="mt-4 text-xl font-bold">{{ $post->title }}</h2>
            @endif

            <p class="mt-3 whitespace-pre-line text-base/80 leading-relaxed">{{ $post->content }}</p>

            @if($post->media->count() > 0)
            <div class="mt-4 grid gap-2 {{ $post->media->count() === 1 ? 'grid-cols-1' : 'grid-cols-2' }}">
                @foreach($post->media as $item)
                <div class="cyber-media rounded-xl overflow-hidden border border-neutral bg-background/40 {{ $post->media->count() === 1 ? 'max-h-[520px]' : 'aspect-video' }}">
                    @if($item->isVideo())
                    <video controls preload="metadata" class="w-full h-full">
                        <source src="{{ $item->url }}">
                    </video>
                    @else
                    <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer">
                        <img src="{{ $item->url }}" alt="" loading="lazy" class="w-full h-full object-cover">
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            <div class="mt-5 flex items-center gap-2 border-t border-neutral pt-4">
                <button wire:click="toggleLike({{ $post->id }})"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold transition cursor-pointer
                    {{ in_array($post->id, $likedPosts, true) ? 'bg-primary/15 text-primary border border-primary/40' : 'border border-neutral text-base/65 hover:text-primary hover:border-primary/40' }}">
                    <x-ri-heart-3-fill class="size-4" />
                    {{ $post->likes_count }}
                </button>

                <button wire:click="toggleComments({{ $post->id }})"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold border border-neutral text-base/65 hover:text-accent hover:border-accent/40 transition cursor-pointer">
                    <x-ri-chat-3-fill class="size-4" />
                    {{ $post->comments_count }} comentarios
                </button>
            </div>

            {{-- Comentarios --}}
            @if(in_array($post->id, $openComments, true))
            <div class="mt-5 space-y-4">
                @auth
                <div class="flex gap-3">
                    <img src="{{ $avatarOf(auth()->user()) }}" alt="avatar" class="size-9 rounded-full border border-neutral object-cover shrink-0">
                    <div class="flex-grow flex gap-2">
                        <input type="text" wire:model="commentBody.{{ $post->id }}"
                            wire:keydown.enter="comment({{ $post->id }})"
                            placeholder="Escribe un comentario..."
                            class="flex-grow rounded-lg border border-neutral bg-background/60 px-4 py-2 text-sm focus:border-primary focus:ring-0">
                        <button wire:click="comment({{ $post->id }})"
                            class="px-3 rounded-lg border border-primary/40 bg-primary/10 text-primary hover:bg-primary/20 transition cursor-pointer">
                            <x-ri-send-plane-fill class="size-4" />
                        </button>
                    </div>
                </div>
                @endauth

                @foreach(($commentsByPost[$post->id] ?? []) as $comment)
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

                    <p class="mt-2 text-sm text-base/80 whitespace-pre-line">{{ $comment->content }}</p>

                    <div class="mt-3 flex items-center gap-3 text-xs">
                        <button wire:click="toggleCommentLike({{ $comment->id }})"
                            class="inline-flex items-center gap-1.5 font-semibold transition cursor-pointer
                            {{ in_array($comment->id, $likedComments, true) ? 'text-primary' : 'text-base/50 hover:text-primary' }}">
                            <x-ri-heart-3-fill class="size-3.5" /> {{ $comment->likes_count }}
                        </button>
                        @auth
                        <button wire:click="$set('replyingTo', {{ $comment->id }})"
                            class="text-base/50 hover:text-accent font-semibold cursor-pointer">
                            Responder
                        </button>
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
                            <p class="mt-1.5 text-sm text-base/75 whitespace-pre-line">{{ $reply->content }}</p>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </article>
        @empty
        <div class="cyber-card cyber-clip p-10 text-center">
            <x-ri-chat-off-line class="size-12 mx-auto text-base/25" />
            <p class="mt-4 text-base/60">Todavía no hay publicaciones. ¡Sé el primero en compartir!</p>
        </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
