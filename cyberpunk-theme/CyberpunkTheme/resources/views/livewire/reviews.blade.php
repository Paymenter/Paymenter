@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;

$avatarOf = fn ($user) => $user ? (Avatars::url($user) ?? $user->avatar) : 'https://www.gravatar.com/avatar/?d=mp';
$title = Config::theme('reviews_name', 'Reseñas');
$canModerate = auth()->check() && auth()->user()->role_id !== null;
@endphp

<div class="container mt-10 pb-16">
    {{-- Cabecera --}}
    <div class="cyber-card cyber-clip p-6 md:p-8 relative overflow-hidden">
        <div class="absolute inset-0 cyber-gradient opacity-[0.07] pointer-events-none"></div>
        <div class="relative flex flex-col md:flex-row md:items-end justify-between gap-5">
            <div>
                <span class="cyber-chip"><x-ri-star-smile-fill class="size-4" /> {{ $title }}</span>
                <h1 class="mt-4 text-3xl md:text-4xl font-black cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ $title }}">{{ $title }}</span>
                </h1>
                <p class="mt-3 text-base/65 max-w-2xl">
                    {{ Config::theme('reviews_description', 'Da tu opinión sobre cualquier plan, dale me gusta y responde a lo que dicen otros clientes.') }}
                </p>
            </div>

            <input type="text" wire:model.live.debounce.400ms="search"
                placeholder="Buscar un plan..."
                class="rounded-lg border border-neutral bg-background/70 text-base text-sm px-4 py-2 focus:border-primary focus:ring-0 w-full md:w-64">
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
    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($products as $product)
        @php
            $s = $stats[$product->id] ?? ['likes' => 0, 'comments' => 0];
            $isPopular = in_array($product->id, $popular, true);
            $isOpen = $openProduct === $product->id;
        @endphp
        <div class="cyber-card cyber-clip flex flex-col overflow-hidden relative {{ $isOpen ? 'border-primary/70' : '' }}">
            @if($isPopular)
            <x-cyber.popular-badge />
            @endif

            @if($product->image)
            <div class="h-32 overflow-hidden">
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            @endif

            <div class="p-4 flex flex-col flex-grow">
                <p class="text-xs text-base/45">{{ $product->category?->name }}</p>
                <h3 class="text-lg font-bold mt-0.5">{{ $product->name }}</h3>

                <div class="mt-3 flex items-center gap-2">
                    <button wire:click="toggleLike({{ $product->id }})"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold transition cursor-pointer
                        {{ in_array($product->id, $likedProducts, true) ? 'bg-primary text-white' : 'border border-neutral text-base/65 hover:text-primary hover:border-primary/50' }}">
                        <x-ri-heart-3-fill class="size-4" /> {{ $s['likes'] }}
                    </button>

                    <button wire:click="open({{ $product->id }})"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold border transition cursor-pointer
                        {{ $isOpen ? 'border-accent/60 bg-accent/15 text-accent' : 'border-neutral text-base/65 hover:text-accent hover:border-accent/50' }}">
                        <x-ri-chat-3-fill class="size-4" /> {{ $s['comments'] }}
                    </button>

                    <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}"
                        wire:navigate class="ml-auto text-xs font-semibold text-primary hover:underline">Ver plan</a>
                </div>
            </div>
        </div>
        @empty
        <div class="cyber-card cyber-clip p-10 text-center col-span-full">
            <x-ri-inbox-2-line class="size-12 mx-auto text-base/25" />
            <p class="mt-4 text-base/60">No hay productos que coincidan con la búsqueda.</p>
        </div>
        @endforelse
    </div>

    {{-- Panel de reseñas del producto abierto --}}
    @if($openedProduct)
    <div class="cyber-card cyber-clip p-6 mt-6" id="resenas">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-accent/10 border border-accent/25">
                    <x-ri-chat-quote-fill class="size-5 text-accent" />
                </div>
                <div>
                    <h2 class="text-xl font-black">Reseñas de {{ $openedProduct->name }}</h2>
                    <p class="text-sm text-base/55">
                        {{ ($stats[$openedProduct->id]['likes'] ?? 0) }} me gusta ·
                        {{ ($stats[$openedProduct->id]['comments'] ?? 0) }} comentarios
                    </p>
                </div>
            </div>
            <button wire:click="open({{ $openedProduct->id }})"
                class="text-base/50 hover:text-error transition cursor-pointer">
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

                        <p class="mt-1 text-sm text-base/80 whitespace-pre-line break-words">{{ $comment->content }}</p>

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
                                    <p class="mt-1 text-sm text-base/75 whitespace-pre-line break-words">{{ $reply->content }}</p>
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
