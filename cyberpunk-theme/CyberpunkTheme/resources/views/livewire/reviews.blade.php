@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Text;

$avatarOf = fn ($user) => $user ? (Avatars::url($user) ?? $user->avatar) : 'https://www.gravatar.com/avatar/?d=mp';
$title = Config::theme('reviews_name', 'Reseñas');
$canModerate = auth()->check() && auth()->user()->role_id !== null;

$totalLikes = collect($stats)->sum('likes');
$totalComments = collect($stats)->sum('comments');
$rated = collect($stats)->filter(fn ($s) => ($s['likes'] ?? 0) + ($s['comments'] ?? 0) > 0)->count();
@endphp

<div class="container mt-10 pb-16">
    {{-- Cabecera --}}
    <div class="cyber-card cyber-clip p-6 md:p-8 relative overflow-hidden">
        <div class="absolute inset-0 cyber-gradient opacity-[0.07] pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div>
                    <span class="cyber-chip"><x-ri-star-smile-fill class="size-4" /> {{ $title }}</span>
                    <h1 class="mt-4 text-3xl md:text-4xl font-black cyber-neon-text">
                        <span class="cyber-glitch" data-text="{{ $title }}">{{ $title }}</span>
                    </h1>
                    <p class="mt-3 text-base/65 max-w-2xl">
                        {{ Config::theme('reviews_description', 'Da tu opinión sobre cualquier plan, dale me gusta y responde a lo que dicen otros clientes.') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[110px]">
                        <div class="text-2xl font-black font-mono neon-text">{{ number_format($totalLikes) }}</div>
                        <div class="text-[10px] uppercase tracking-widest text-base/55 mt-0.5">Me gusta</div>
                    </div>
                    <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[110px]">
                        <div class="text-2xl font-black font-mono neon-text-accent">{{ number_format($totalComments) }}</div>
                        <div class="text-[10px] uppercase tracking-widest text-base/55 mt-0.5">Opiniones</div>
                    </div>
                    <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[110px]">
                        <div class="text-2xl font-black font-mono neon-text">{{ number_format($rated) }}</div>
                        <div class="text-[10px] uppercase tracking-widest text-base/55 mt-0.5">Planes valorados</div>
                    </div>
                </div>
            </div>

            <div class="cyber-divider my-6"></div>

            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <x-ri-search-line class="size-4 text-base/40 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input type="text" wire:model.live.debounce.400ms="search"
                        placeholder="Buscar un plan..."
                        class="w-full rounded-lg border border-neutral bg-background/70 text-base text-sm ps-10 pe-4 py-2.5 focus:border-primary focus:ring-0">
                </div>
                <select wire:model.live="sort"
                    class="rounded-lg border border-neutral bg-background/70 text-base text-sm px-3 py-2.5 focus:border-primary focus:ring-0 sm:w-56">
                    <option value="valorados">Más valorados</option>
                    <option value="comentados">Más comentados</option>
                    <option value="nombre">Por nombre</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Filtro por categoría --}}
    @if($categories->count() > 0)
    <div class="mt-5 flex flex-wrap gap-2">
        <button wire:click="$set('categoryFilter', '')"
            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition cursor-pointer
            {{ $categoryFilter === '' ? 'border-primary bg-primary/15 text-primary' : 'border-neutral text-base/65 hover:border-primary/50 hover:text-primary' }}">
            <x-ri-apps-2-fill class="size-4" /> Todos
        </button>
        @foreach($categories as $cat)
        <button wire:click="$set('categoryFilter', '{{ $cat->id }}')"
            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition cursor-pointer
            {{ (string) $categoryFilter === (string) $cat->id ? 'border-primary bg-primary/15 text-primary' : 'border-neutral text-base/65 hover:border-primary/50 hover:text-primary' }}">
            {{ $cat->name }}
        </button>
        @endforeach
    </div>
    @endif

    {{-- Productos --}}
    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($products as $product)
        @php
            $s = $stats[$product->id] ?? ['likes' => 0, 'comments' => 0];
            $isPopular = in_array($product->id, $popular, true);
            $isOpen = $openProduct === $product->id;
            $isLiked = in_array($product->id, $likedProducts, true);
            $price = null;
            try { $price = $product->price()?->formatted?->price; } catch (\Throwable $e) { $price = null; }
        @endphp
        <div wire:key="review-card-{{ $product->id }}"
            class="cyber-card cyber-card-hover cyber-clip flex flex-col overflow-hidden relative transition
            {{ $isOpen ? 'border-primary/70 cyber-neon' : '' }} {{ $isPopular ? 'cyber-popular-card' : '' }}">
            @if($isPopular)
            <x-cyber.popular-badge />
            @endif

            {{-- Portada: imagen completa o una cabecera de color si no hay imagen --}}
            @if($product->image)
            {{-- Miniatura uniforme: en esta rejilla queda mucho más limpio --}}
            <x-cyber.picture :src="Storage::url($product->image)" :alt="$product->name" height="h-40" mode="cover" />
            @else
            <div class="relative h-40 overflow-hidden">
                <div class="absolute inset-0 cyber-gradient opacity-30"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <x-ri-price-tag-3-fill class="size-10 text-primary/70" />
                </div>
                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-background-secondary to-transparent"></div>
            </div>
            @endif

            <div class="p-5 flex flex-col flex-grow">
                @if($product->category?->name)
                <span class="w-fit rounded-md border border-neutral bg-background/50 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider text-base/55">
                    {{ $product->category->name }}
                </span>
                @endif

                <h3 class="text-lg font-bold mt-2 leading-snug">{{ $product->name }}</h3>

                @if($price)
                <p class="mt-1 text-sm font-black cyber-gradient-text">{{ $price }}</p>
                @endif

                {{-- Resumen de valoraciones --}}
                <div class="mt-4 flex items-center gap-4 text-sm">
                    <span class="inline-flex items-center gap-1.5 text-base/60">
                        <x-ri-heart-3-fill class="size-4 text-primary" />
                        <span class="font-bold text-base/85">{{ $s['likes'] }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-base/60">
                        <x-ri-chat-3-fill class="size-4 text-accent" />
                        <span class="font-bold text-base/85">{{ $s['comments'] }}</span>
                    </span>
                    @if(($s['likes'] + $s['comments']) === 0)
                    <span class="ms-auto text-xs text-base/40">Sin reseñas</span>
                    @endif
                </div>

                <div class="mt-auto pt-5 flex items-center gap-2">
                    <button wire:click="toggleLike({{ $product->id }})"
                        title="Me gusta"
                        class="inline-flex items-center justify-center size-10 rounded-lg font-bold transition cursor-pointer shrink-0
                        {{ $isLiked ? 'bg-primary text-inverted border border-primary' : 'border border-neutral text-base/60 hover:text-primary hover:border-primary/50' }}">
                        <x-ri-heart-3-fill class="size-5" />
                    </button>

                    <button wire:click="open({{ $product->id }})"
                        class="flex-grow inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-bold border transition cursor-pointer
                        {{ $isOpen ? 'border-accent/60 bg-accent/15 text-accent' : 'border-primary/40 bg-primary/10 text-primary hover:bg-primary/20' }}">
                        <x-ri-chat-quote-fill class="size-4" />
                        {{ $isOpen ? 'Cerrar' : 'Ver reseñas' }}
                    </button>

                    <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}"
                        wire:navigate title="Ver el plan"
                        class="inline-flex items-center justify-center size-10 rounded-lg border border-neutral text-base/60 hover:text-primary hover:border-primary/50 transition shrink-0">
                        <x-ri-external-link-line class="size-4" />
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="cyber-card cyber-clip p-10 text-center col-span-full">
            <x-ri-inbox-2-line class="size-12 mx-auto text-base/25" />
            <p class="mt-4 text-base/60">No hay planes que coincidan con la búsqueda.</p>
        </div>
        @endforelse
    </div>

    {{-- Panel de reseñas del producto abierto --}}
    @if($openedProduct)
    <div class="cyber-card cyber-clip p-6 mt-6 scroll-mt-24" id="resenas"
        x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start' }))">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                @if($openedProduct->image)
                <img src="{{ Storage::url($openedProduct->image) }}" alt="{{ $openedProduct->name }}"
                    class="size-14 rounded-xl border border-neutral object-cover shrink-0">
                @else
                <div class="p-2.5 rounded-xl bg-accent/10 border border-accent/25 shrink-0">
                    <x-ri-chat-quote-fill class="size-6 text-accent" />
                </div>
                @endif
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-widest text-base/45">{{ $openedProduct->category?->name }}</p>
                    <h2 class="text-xl font-black truncate">Reseñas de {{ $openedProduct->name }}</h2>
                    <p class="text-sm text-base/55 flex flex-wrap items-center gap-x-3">
                        <span class="inline-flex items-center gap-1.5">
                            <x-ri-heart-3-fill class="size-3.5 text-primary" />
                            {{ ($stats[$openedProduct->id]['likes'] ?? 0) }} me gusta
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <x-ri-chat-3-fill class="size-3.5 text-accent" />
                            {{ ($stats[$openedProduct->id]['comments'] ?? 0) }} opiniones
                        </span>
                    </p>
                </div>
            </div>
            <button wire:click="open({{ $openedProduct->id }})" title="Cerrar"
                class="p-2 rounded-lg border border-neutral text-base/50 hover:text-error hover:border-error/40 transition cursor-pointer">
                <x-ri-close-fill class="size-5" />
            </button>
        </div>

        <div class="cyber-divider my-5"></div>

        @auth
        <div class="flex gap-3">
            <img src="{{ $avatarOf(auth()->user()) }}" alt="avatar" class="size-10 rounded-full border border-neutral object-cover shrink-0">
            <div class="flex-grow">
                <textarea wire:model="body" rows="2" maxlength="1500"
                    placeholder="¿Qué te parece este plan? Cuenta tu experiencia..."
                    class="w-full rounded-lg border border-neutral bg-background/60 text-base px-4 py-2.5 text-sm focus:border-primary focus:ring-0"></textarea>
                <div class="mt-2 flex justify-end">
                    <x-button.primary wire:click="comment({{ $openedProduct->id }})" wire:loading.attr="disabled" class="!w-fit">
                        <x-ri-send-plane-fill class="size-4" /> Publicar reseña
                    </x-button.primary>
                </div>
            </div>
        </div>
        @else
        <div class="rounded-xl border border-neutral bg-background/40 p-4 text-center text-sm text-base/65">
            <a href="{{ route('login') }}" wire:navigate class="text-primary font-semibold hover:underline">Inicia sesión</a>
            para dejar tu reseña.
        </div>
        @endauth

        <div class="mt-5 space-y-3">
            @forelse($comments as $comment)
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
                            <button wire:click="deleteComment({{ $comment->id }})" wire:confirm="¿Eliminar reseña?"
                                class="text-base/30 hover:text-error transition cursor-pointer shrink-0">
                                <x-ri-delete-bin-6-line class="size-3.5" />
                            </button>
                            @endif
                        </div>

                        <p class="mt-1 text-sm text-base/80 whitespace-pre-line break-words">{!! Text::linkify($comment->content) !!}</p>

                        <div class="mt-2 flex items-center gap-3 text-xs">
                            <button wire:click="toggleCommentLike({{ $comment->id }})"
                                class="inline-flex items-center gap-1.5 font-semibold text-base/50 hover:text-primary transition cursor-pointer">
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
            @empty
            <p class="text-sm text-base/50 text-center py-6">Todavía no hay reseñas de este plan. ¡Sé el primero!</p>
            @endforelse
        </div>
    </div>
    @endif
</div>
