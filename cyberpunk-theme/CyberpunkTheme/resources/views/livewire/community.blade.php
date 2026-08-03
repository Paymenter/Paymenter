@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\PostCategories;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Text;

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
                <span class="cyber-chip"><x-ri-chat-smile-2-fill class="size-4" /> {{ $communityName }}</span>
                <h1 class="mt-4 text-3xl md:text-4xl font-black cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ $communityName }}">{{ $communityName }}</span>
                </h1>
                <p class="mt-3 text-base/65 max-w-2xl">
                    {{ Config::theme('community_description', 'Pide ayuda, comparte soluciones, enseña tus logros y propón ideas nuevas. Aquí nos ayudamos entre todos.') }}
                </p>
            </div>

            <select wire:model.live="sort"
                class="rounded-lg border border-neutral bg-background/70 text-base text-sm px-3 py-2 focus:border-primary focus:ring-0">
                <option value="recent">Más recientes</option>
                <option value="liked">Más gustadas</option>
                <option value="commented">Más comentadas</option>
            </select>
        </div>
    </div>

    {{-- Filtros por categoría --}}
    <div class="mt-5 flex flex-wrap gap-2">
        <button wire:click="setFilter('')"
            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition cursor-pointer
            {{ $filter === '' ? 'border-primary bg-primary/15 text-primary' : 'border-neutral text-base/65 hover:border-primary/50 hover:text-primary' }}">
            <x-ri-apps-2-fill class="size-4" />
            Todo
        </button>

        @foreach($categories as $key => $cat)
        <button wire:click="setFilter('{{ $key }}')"
            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition cursor-pointer
            {{ $filter === $key ? 'text-inverted' : 'border-neutral text-base/65 hover:text-base' }}"
            @style([
                'border-color: ' . $cat['hex'] . ($filter === $key ? '' : '55'),
                'background: ' . $cat['hex'] . ($filter === $key ? '' : '15'),
                'color: ' . ($filter === $key ? '#fff' : $cat['hex']),
            ])>
            <x-dynamic-component :component="$cat['icon']" class="size-4" />
            {{ $cat['short'] }}
            @if(!empty($counts[$key]))
            <span class="opacity-70">{{ $counts[$key] }}</span>
            @endif
        </button>
        @endforeach
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
    <div class="cyber-card cyber-clip p-5 mt-6">
        <div class="flex gap-3">
            <img src="{{ $avatarOf(auth()->user()) }}" alt="avatar"
                class="size-10 rounded-full border border-primary/40 object-cover shrink-0">
            <div class="flex-grow min-w-0">
                {{-- Categoría --}}
                <p class="text-xs font-semibold uppercase tracking-widest text-base/50 mb-2">¿Qué vas a publicar?</p>
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($categories as $key => $cat)
                    <button type="button" wire:click="$set('category', '{{ $key }}')"
                        class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-bold transition cursor-pointer"
                        @style([
                            'border-color: ' . $cat['hex'] . ($category === $key ? '' : '40'),
                            'background: ' . $cat['hex'] . ($category === $key ? '30' : '12'),
                            'color: ' . $cat['hex'],
                        ])>
                        <x-dynamic-component :component="$cat['icon']" class="size-3.5" />
                        {{ $cat['short'] }}
                    </button>
                    @endforeach
                </div>
                <p class="text-xs text-base/45 mb-3">{{ PostCategories::get($category)['description'] }}</p>

                <input type="text" wire:model="title" maxlength="120"
                    placeholder="Título (opcional)"
                    class="w-full rounded-lg border border-neutral bg-background/60 text-base px-4 py-2.5 text-sm mb-3 focus:border-primary focus:ring-0">
                @error('title') <p class="text-error text-xs mb-2">{{ $message }}</p> @enderror

                <textarea wire:model="content" rows="3" maxlength="2000"
                    placeholder="Cuenta tu caso, tu solución o tu logro..."
                    class="w-full rounded-lg border border-neutral bg-background/60 text-base px-4 py-3 text-sm focus:border-primary focus:ring-0"></textarea>
                @error('content') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-primary cursor-pointer hover:opacity-80">
                        <x-ri-image-add-fill class="size-5" />
                        Añadir fotos o vídeos
                        <input type="file" wire:model="media" multiple accept="image/*,video/mp4,video/webm,video/quicktime" class="hidden">
                    </label>
                    <span class="text-xs text-base/45">Máx. {{ Config::theme('community_media_limit', 4) }} archivos · 20 MB c/u</span>
                    <span wire:loading wire:target="media" class="text-xs text-primary font-semibold">Subiendo archivos...</span>
                </div>
                @error('media.*') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                @error('media') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

                @if(count($media) > 0)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($media as $file)
                    <div class="size-20 rounded-lg overflow-hidden border border-primary/40 bg-background/60 flex items-center justify-center">
                        @if(str_starts_with($file->getMimeType() ?? '', 'image'))
                        <img src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover" alt="">
                        @else
                        <x-ri-film-fill class="size-8 text-primary" />
                        @endif
                    </div>
                    @endforeach
                    <span class="self-center text-xs text-success font-semibold">{{ count($media) }} listo(s) para publicar</span>
                </div>
                @endif

                <div class="mt-4 flex justify-end">
                    <x-button.primary wire:click="publish" wire:loading.attr="disabled" wire:target="publish" class="!w-fit">
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
    <div class="mt-6 space-y-4">
        @forelse($posts as $post)
        @php $cat = PostCategories::get($post->category ?? 'general'); @endphp
        <article class="cyber-card cyber-clip p-5">
            <div class="flex gap-3">
                <img src="{{ $avatarOf($post->user) }}" alt="avatar"
                    class="size-10 rounded-full border border-neutral object-cover shrink-0">

                <div class="flex-grow min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 min-w-0">
                            <span class="font-bold truncate">{{ $post->user?->name ?? 'Usuario' }}</span>
                            <span class="text-xs text-base/45">· {{ $post->created_at->diffForHumans() }}</span>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-bold"
                                @style(['background: ' . $cat['hex'] . '20', 'color: ' . $cat['hex']])>
                                <x-dynamic-component :component="$cat['icon']" class="size-3" />
                                {{ $cat['short'] }}
                            </span>
                            @if($post->pinned)
                            <span class="inline-flex items-center gap-1 rounded-full bg-primary/15 text-primary px-2 py-0.5 text-[11px] font-bold">
                                <x-ri-pushpin-fill class="size-3" /> Destacado
                            </span>
                            @endif
                        </div>

                        @if(auth()->check() && (auth()->id() === $post->user_id || $canModerate))
                        <button wire:click="deletePost({{ $post->id }})" wire:confirm="¿Eliminar esta publicación?"
                            class="text-base/35 hover:text-error transition cursor-pointer shrink-0" title="Eliminar">
                            <x-ri-delete-bin-6-line class="size-4" />
                        </button>
                        @endif
                    </div>

                    @if($post->title)
                    <h2 class="mt-2 text-lg font-bold">{{ $post->title }}</h2>
                    @endif

                    <p class="mt-1.5 whitespace-pre-line text-base/80 leading-relaxed break-words">{!! Text::linkify($post->content) !!}</p>

                    @if($post->media->count() > 0)
                    @php $single = $post->media->count() === 1; @endphp
                    {{-- Estilo Facebook: un solo archivo se ve completo (la caja se
                         adapta a la imagen o al vídeo); varios van en rejilla. --}}
                    <div class="mt-3 grid gap-2 max-w-2xl {{ $single ? 'grid-cols-1' : 'grid-cols-2' }}">
                        @foreach($post->media as $item)
                        <div class="rounded-xl overflow-hidden border border-neutral
                            {{ $single ? 'cyber-media-full' : 'cyber-media aspect-square bg-background/40' }}">
                            @if($item->isVideo())
                            <video controls playsinline preload="metadata"
                                class="bg-black {{ $single ? 'cyber-media-item' : '' }}">
                                <source src="{{ $item->url }}">
                                Tu navegador no puede reproducir este vídeo.
                            </video>
                            @else
                            @if($single)
                            <img src="{{ $item->url }}" alt="" aria-hidden="true" class="cyber-media-bg">
                            @endif
                            <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="block">
                                <img src="{{ $item->url }}" alt="" loading="lazy"
                                    class="{{ $single ? 'cyber-media-item' : '' }}">
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="mt-4 flex items-center gap-2 border-t border-neutral pt-3">
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
                    <div class="mt-4 space-y-3">
                        @auth
                        <div class="flex gap-2">
                            <img src="{{ $avatarOf(auth()->user()) }}" alt="avatar" class="size-8 rounded-full border border-neutral object-cover shrink-0">
                            <input type="text" wire:model="commentBody.{{ $post->id }}"
                                wire:keydown.enter="comment({{ $post->id }})"
                                placeholder="Escribe un comentario..."
                                class="flex-grow rounded-lg border border-neutral bg-background/60 text-base px-4 py-2 text-sm focus:border-primary focus:ring-0">
                            <button wire:click="comment({{ $post->id }})"
                                class="px-3 rounded-lg border border-primary/40 bg-primary/10 text-primary hover:bg-primary/20 transition cursor-pointer">
                                <x-ri-send-plane-fill class="size-4" />
                            </button>
                        </div>
                        @endauth

                        @foreach(($commentsByPost[$post->id] ?? []) as $comment)
                        <div class="rounded-xl border border-neutral bg-background/40 p-3">
                            <div class="flex gap-2.5">
                                <img src="{{ $avatarOf($comment->user) }}" alt="avatar" class="size-8 rounded-full border border-neutral object-cover shrink-0">
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex flex-wrap items-center gap-x-2 min-w-0">
                                            <span class="font-semibold text-sm truncate">{{ $comment->user?->name ?? 'Usuario' }}</span>
                                            <span class="text-xs text-base/45">· {{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if(auth()->check() && (auth()->id() === $comment->user_id || $canModerate))
                                        <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="¿Eliminar comentario?"
                                            class="text-base/30 hover:text-error transition cursor-pointer shrink-0">
                                            <x-ri-delete-bin-6-line class="size-3.5" />
                                        </button>
                                        @endif
                                    </div>

                                    <p class="mt-1 text-sm text-base/80 whitespace-pre-line break-words">{!! Text::linkify($comment->content) !!}</p>

                                    <div class="mt-2 flex items-center gap-3 text-xs">
                                        <button wire:click="toggleCommentLike({{ $comment->id }})"
                                            class="inline-flex items-center gap-1.5 font-semibold transition cursor-pointer
                                            {{ in_array($comment->id, $likedComments, true) ? 'text-primary' : 'text-base/50 hover:text-primary' }}">
                                            <x-ri-heart-3-fill class="size-3.5" /> {{ $comment->likes_count }}
                                        </button>
                                        @auth
                                        <button wire:click="$set('replyingTo', {{ $comment->id }})"
                                            class="inline-flex items-center gap-1 text-base/50 hover:text-accent font-semibold cursor-pointer">
                                            <x-ri-reply-fill class="size-3.5" /> Responder
                                        </button>
                                        @endauth
                                    </div>

                                    @if($replyingTo === $comment->id)
                                    <div class="mt-2.5">
                                        <p class="text-xs text-accent font-semibold mb-1.5 flex items-center gap-1">
                                            <x-ri-corner-down-right-line class="size-3.5" />
                                            Respondiendo a {{ $comment->user?->name ?? 'Usuario' }}
                                        </p>
                                        <div class="flex gap-2">
                                            <input type="text" wire:model="replyBody.{{ $comment->id }}"
                                                wire:keydown.enter="reply({{ $comment->id }})"
                                                placeholder="Tu respuesta..."
                                                class="flex-grow rounded-lg border border-accent/40 bg-background/60 text-base px-3 py-1.5 text-sm focus:border-accent focus:ring-0">
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
                                    <div class="mt-3 space-y-2.5 ps-4 border-s-2 border-accent/30">
                                        @foreach($comment->replies as $reply)
                                        @if($reply->approved)
                                        <div class="flex gap-2">
                                            <img src="{{ $avatarOf($reply->user) }}" alt="avatar" class="size-7 rounded-full border border-neutral object-cover shrink-0">
                                            <div class="flex-grow min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="flex flex-wrap items-center gap-x-2 min-w-0">
                                                        <span class="font-semibold text-sm truncate">{{ $reply->user?->name ?? 'Usuario' }}</span>
                                                        <span class="inline-flex items-center gap-1 text-[11px] text-accent font-semibold">
                                                            <x-ri-corner-down-right-line class="size-3" />
                                                            en respuesta a {{ $comment->user?->name ?? 'Usuario' }}
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
                                                <p class="mt-1 text-sm text-base/75 whitespace-pre-line break-words">{!! Text::linkify($reply->content) !!}</p>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </article>
        @empty
        <div class="cyber-card cyber-clip p-10 text-center">
            <x-ri-chat-off-line class="size-12 mx-auto text-base/25" />
            <p class="mt-4 text-base/60">
                @if($filter !== '')
                No hay publicaciones en esta categoría todavía.
                @else
                Todavía no hay publicaciones. ¡Sé el primero en compartir!
                @endif
            </p>
        </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
