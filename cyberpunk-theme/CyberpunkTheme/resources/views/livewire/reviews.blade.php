@php
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Avatars;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews as ReviewsHelper;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Text;

$avatarOf = fn ($user) => $user ? (Avatars::url($user) ?? $user->avatar) : 'https://www.gravatar.com/avatar/?d=mp';
$title = Config::theme('reviews_name', 'Reseñas');
$canModerate = auth()->check() && auth()->user()->role_id !== null;

$totalReviews = collect($stats)->sum('count') + ($generalStats['count'] ?? 0);
$rated = collect($stats)->filter(fn ($s) => ($s['count'] ?? 0) > 0)->count();

$mediaGlobal = 0;
$sumaGlobal = 0;
$nGlobal = 0;
foreach ($stats as $s) {
    $sumaGlobal += ($s['average'] ?? 0) * ($s['count'] ?? 0);
    $nGlobal += $s['count'] ?? 0;
}
$sumaGlobal += ($generalStats['average'] ?? 0) * ($generalStats['count'] ?? 0);
$nGlobal += $generalStats['count'] ?? 0;
$mediaGlobal = $nGlobal > 0 ? round($sumaGlobal / $nGlobal, 1) : 0;
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
                        {{ Config::theme('reviews_description', 'Puntúa con estrellas y cuenta tu experiencia. Para dejar una valoración hay que escribir una reseña.') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[130px]">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-black font-mono neon-text">{{ $nGlobal > 0 ? number_format($mediaGlobal, 1, ',', '.') : '—' }}</span>
                            <span class="text-xs text-base/45">/ 5</span>
                        </div>
                        <x-cyber.stars :value="$mediaGlobal" size="size-3.5" class="mt-1" />
                        <div class="text-[10px] uppercase tracking-widest text-base/55 mt-1">Nota media</div>
                    </div>
                    <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[110px]">
                        <div class="text-2xl font-black font-mono neon-text-accent">{{ number_format($totalReviews) }}</div>
                        <div class="text-[10px] uppercase tracking-widest text-base/55 mt-0.5">Reseñas</div>
                    </div>
                    <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[110px]">
                        <div class="text-2xl font-black font-mono neon-text">{{ number_format($rated) }}</div>
                        <div class="text-[10px] uppercase tracking-widest text-base/55 mt-0.5">Planes valorados</div>
                    </div>
                </div>
            </div>

            <div class="cyber-divider my-6"></div>

            {{-- Los dos apartados --}}
            <div class="flex flex-wrap gap-2">
                <button wire:click="setSection('planes')"
                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-bold transition cursor-pointer
                    {{ $section === 'planes' ? 'border-primary bg-primary/15 text-primary' : 'border-neutral text-base/65 hover:border-primary/50 hover:text-primary' }}">
                    <x-ri-price-tag-3-fill class="size-4" />
                    Reseñas de los planes
                </button>
                <button wire:click="setSection('general')"
                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-bold transition cursor-pointer
                    {{ $section === 'general' ? 'border-accent bg-accent/15 text-accent' : 'border-neutral text-base/65 hover:border-accent/50 hover:text-accent' }}">
                    <x-ri-shield-star-fill class="size-4" />
                    {{ $generalName }}
                    @if(($generalStats['count'] ?? 0) > 0)
                    <span class="opacity-70">{{ $generalStats['count'] }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- ============================ PLANES ============================ --}}
    @if($section === 'planes')

    <div class="mt-6 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-grow">
            <x-ri-search-line class="size-4 text-base/40 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
            <input type="text" wire:model.live.debounce.400ms="search"
                placeholder="Buscar un plan..."
                class="w-full rounded-lg border border-neutral bg-background/70 text-base text-sm ps-10 pe-4 py-2.5 focus:border-primary focus:ring-0">
        </div>
        <select wire:model.live="sort"
            class="rounded-lg border border-neutral bg-background/70 text-base text-sm px-3 py-2.5 focus:border-primary focus:ring-0 sm:w-56">
            <option value="valorados">Mejor valorados</option>
            <option value="comentados">Con más reseñas</option>
            <option value="nombre">Por nombre</option>
        </select>
    </div>

    @if($categories->count() > 0)
    <div class="mt-4 flex flex-wrap gap-2">
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

    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($products as $product)
        @php
            $s = $stats[$product->id] ?? ReviewsHelper::EMPTY;
            $isPopular = in_array($product->id, $popular, true);
            $isOpen = $openProduct === $product->id;
            $price = null;
            try { $price = $product->price()?->formatted?->price; } catch (\Throwable $e) { $price = null; }
        @endphp
        <div wire:key="review-card-{{ $product->id }}"
            class="cyber-card cyber-card-hover cyber-clip flex flex-col overflow-hidden relative transition
            {{ $isOpen ? 'border-primary/70 cyber-neon' : '' }} {{ $isPopular ? 'cyber-popular-card' : '' }}">
            @if($isPopular)
            <x-cyber.popular-badge label="MEJOR VALORADO" />
            @endif

            @if($product->image)
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

                <div class="mt-4 flex items-center gap-2 flex-wrap">
                    <x-cyber.stars :value="$s['average']" size="size-4" />
                    @if($s['count'] > 0)
                    <span class="text-sm font-bold text-base/85">{{ number_format($s['average'], 1, ',', '.') }}</span>
                    <span class="text-xs text-base/50">({{ $s['count'] }})</span>
                    @else
                    <span class="text-xs text-base/40">Sin reseñas</span>
                    @endif
                </div>

                <div class="mt-auto pt-5 flex items-center gap-2">
                    <button wire:click="open({{ $product->id }})"
                        class="flex-grow inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-bold border transition cursor-pointer
                        {{ $isOpen ? 'border-accent/60 bg-accent/15 text-accent' : 'border-primary/40 bg-primary/10 text-primary hover:bg-primary/20' }}">
                        <x-ri-star-smile-fill class="size-4" />
                        {{ $isOpen ? 'Cerrar' : ($s['count'] > 0 ? 'Ver reseñas' : 'Ser el primero') }}
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

    {{-- Panel del plan abierto --}}
    @if($openedProduct)
    @php $s = $stats[$openedProduct->id] ?? ReviewsHelper::EMPTY; @endphp
    <div class="cyber-card cyber-clip p-6 mt-6 scroll-mt-24" id="resenas"
        x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start' }))">

        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                @if($openedProduct->image)
                <img src="{{ Storage::url($openedProduct->image) }}" alt="{{ $openedProduct->name }}"
                    class="size-14 rounded-xl border border-neutral object-cover shrink-0">
                @else
                <div class="p-2.5 rounded-xl bg-accent/10 border border-accent/25 shrink-0">
                    <x-ri-star-smile-fill class="size-6 text-accent" />
                </div>
                @endif
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-widest text-base/45">{{ $openedProduct->category?->name }}</p>
                    <h2 class="text-xl font-black truncate">{{ $openedProduct->name }}</h2>
                    <p class="text-sm text-base/55">{{ ReviewsHelper::summaryLabel($s) }}</p>
                </div>
            </div>
            <button wire:click="open({{ $openedProduct->id }})" title="Cerrar"
                class="p-2 rounded-lg border border-neutral text-base/50 hover:text-error hover:border-error/40 transition cursor-pointer">
                <x-ri-close-fill class="size-5" />
            </button>
        </div>

        @if($s['count'] > 0)
        <div class="cyber-divider my-5"></div>
        <x-cyber.rating-summary :stats="$s" />
        @endif

        <div class="cyber-divider my-5"></div>

        {{-- Dejar reseña --}}
        <x-cyber.review-form
            :rating="$rating"
            model="rating"
            body="body"
            :value="$body"
            :action="'publishReview(' . $openedProduct->id . ')'"
            :own="auth()->check() ? ReviewsHelper::userReview(auth()->id(), \App\Models\Product::class, $openedProduct->id) : null"
            placeholder="¿Qué tal te ha ido con este plan? Cuenta tu experiencia..." />

        {{-- Listado --}}
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
    </div>
    @endif

    {{-- =========================== GENERAL ============================ --}}
    @else

    <div class="cyber-card cyber-clip p-6 md:p-8 mt-6">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-accent/10 border border-accent/25 shrink-0">
                <x-ri-shield-star-fill class="size-6 text-accent" />
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-black">{{ $generalName }}</h2>
                <p class="text-sm text-base/55">
                    {{ Config::theme('general_reviews_description', 'Qué opinan los clientes del servicio en conjunto: soporte, velocidad, estabilidad y trato.') }}
                </p>
            </div>
        </div>

        @if(($generalStats['count'] ?? 0) > 0)
        <div class="cyber-divider my-6"></div>
        <x-cyber.rating-summary :stats="$generalStats" />
        @endif

        <div class="cyber-divider my-6"></div>

        <x-cyber.review-form
            :rating="$generalRating"
            model="generalRating"
            body="generalBody"
            :value="$generalBody"
            action="publishGeneralReview"
            :own="auth()->check() ? ReviewsHelper::userReview(auth()->id(), \Paymenter\Extensions\Others\CyberpunkTheme\Models\Comment::GENERAL, 0) : null"
            placeholder="¿Cómo ha sido tu experiencia con el hosting? Soporte, velocidad, estabilidad..." />
    </div>

    <div class="mt-6 space-y-3">
        @forelse($generalReviews as $comment)
        <div class="cyber-card cyber-clip p-4">
            <x-cyber.review-item :comment="$comment" :likedComments="$likedComments"
                :replyingTo="$replyingTo" :replyBody="$replyBody" :canModerate="$canModerate" :bare="true" />
        </div>
        @empty
        <div class="cyber-card cyber-clip p-10 text-center">
            <x-ri-chat-quote-line class="size-12 mx-auto text-base/25" />
            <p class="mt-4 text-base/60">Todavía no hay opiniones sobre el servicio. ¡Cuéntanos la tuya!</p>
        </div>
        @endforelse
    </div>

    @endif
</div>
