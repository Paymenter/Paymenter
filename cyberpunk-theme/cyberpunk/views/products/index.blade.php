@php
$totalProducts = count($products);
$limited = collect($products)->filter(fn ($p) => $p->stock !== null);
$stockTotal = $limited->sum('stock');
$soldOut = collect($products)->filter(fn ($p) => $p->stock !== null && $p->stock <= 0)->count();
$popular = cyber_ext() && cyber_bool('reviews_enabled', true)
    ? \Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews::popularProductIds()
    : [];
@endphp

<div class="container mt-10 pb-10">
    {{-- Cabecera de categoría --}}
    {{-- La imagen se ve completa (se adapta a su proporción) y el texto va
         encima, sobre un degradado, para que siempre se lea bien. --}}
    <div class="cyber-card cyber-clip relative overflow-hidden grid">
        @if($category->image)
        @php $full = cyber_image_mode() !== 'cover'; @endphp
        @if($full)
        {{-- Copia desenfocada de fondo para las imágenes muy altas --}}
        <img src="{{ Storage::url($category->image) }}" alt="" aria-hidden="true"
            class="col-start-1 row-start-1 self-stretch w-full h-full object-cover scale-110 blur-2xl opacity-40">
        @endif
        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
            class="col-start-1 row-start-1 self-start block w-full object-center
            {{ $full ? 'h-auto max-h-[85vh] object-contain' : 'h-48 md:h-64 object-cover' }}">
        <div class="col-start-1 row-start-1 self-stretch pointer-events-none bg-gradient-to-t from-background-secondary via-background-secondary/60 to-background-secondary/10"></div>
        @endif

        <div class="col-start-1 row-start-1 self-end p-6 md:p-8 relative">
        <div class="absolute inset-0 cyber-gradient opacity-[0.07] pointer-events-none"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <span class="cyber-chip"><x-ri-shopping-bag-3-fill class="size-3.5" /> Tienda</span>
                <h1 class="mt-4 text-3xl md:text-4xl font-black cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ $category->name }}">{{ $category->name }}</span>
                </h1>
                @if($category->description)
                <article class="prose dark:prose-invert mt-3 max-w-2xl text-base/70">
                    {!! $category->description !!}
                </article>
                @endif
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[130px]">
                    <div class="text-2xl font-black font-mono neon-text">{{ $totalProducts }}</div>
                    <div class="text-[10px] uppercase tracking-widest text-base/55 mt-0.5">Productos</div>
                </div>
                <div class="rounded-xl border border-neutral bg-background/60 px-4 py-3 min-w-[130px]">
                    <div class="text-2xl font-black font-mono {{ $limited->count() === 0 || $stockTotal > 0 ? 'neon-text-accent' : 'text-error' }}">
                        {{ $limited->count() === 0 ? '∞' : $stockTotal }}
                    </div>
                    <div class="text-[10px] uppercase tracking-widest text-base/55 mt-0.5">Stock disponible</div>
                </div>
                @if($soldOut > 0)
                <div class="rounded-xl border border-error/40 bg-error/10 px-4 py-3 min-w-[130px]">
                    <div class="text-2xl font-black font-mono text-error">{{ $soldOut }}</div>
                    <div class="text-[10px] uppercase tracking-widest text-error/80 mt-0.5">Agotados</div>
                </div>
                @endif
            </div>
        </div>
        </div>
    </div>

    <div class="flex flex-col md:grid md:grid-cols-4 gap-6 mt-6">
        {{-- Categorías --}}
        <div class="flex flex-col gap-4">
            <div class="cyber-card cyber-clip p-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-base/55 mb-3">Categorías</h2>
                <div class="flex flex-col">
                    @foreach ($categories as $ccategory)
                    <a href="{{ route('category.show', ['category' => $ccategory->slug]) }}" wire:navigate
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition
                        {{ $category->id == $ccategory->id ? 'bg-primary/15 text-primary font-bold border border-primary/30' : 'hover:bg-primary/5 text-base/75' }}">
                        <x-ri-arrow-right-s-line class="size-4" />
                        {{ $ccategory->name }}
                    </a>
                    @endforeach
                </div>
            </div>

            @if(cyber_bool('socials_enabled', true) && count(cyber_socials()) > 0)
            <div class="cyber-card cyber-clip p-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-base/55 mb-3">Síguenos</h2>
                <x-cyber.socials :compact="true" />
            </div>
            @endif
        </div>

        <div class="flex flex-col gap-6 col-span-3">
            {{-- Subcategorías --}}
            @if (count($childCategories) >= 1)
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 h-fit">
                @foreach ($childCategories as $childCategory)
                <div class="cyber-card cyber-card-hover cyber-clip flex flex-col overflow-hidden">
                    @if ($childCategory->image)
                    <x-cyber.picture :src="Storage::url($childCategory->image)" :alt="$childCategory->name"
                        :height="cyber_bool('small_images', false) ? 'h-24' : 'h-36'" max="max-h-[22rem]" />
                    @endif
                    <div class="p-4 flex flex-col flex-grow">
                        <h2 class="text-lg font-bold">{{ $childCategory->name }}</h2>
                        @if(cyber_bool('show_category_description', true) && $childCategory->description)
                        <article class="mt-2 prose prose-sm dark:prose-invert text-base/65 line-clamp-3">
                            {!! $childCategory->description !!}
                        </article>
                        @endif
                        <a href="{{ route('category.show', ['category' => $childCategory->slug]) }}" wire:navigate class="mt-auto pt-4">
                            <x-button.primary>{{ __('common.button.view') }}</x-button.primary>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Productos --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 h-fit">
                @forelse ($products as $product)
                @php
                    $isSoldOut = $product->stock !== null && $product->stock <= 0;
                    $isPopular = in_array($product->id, $popular, true);
                    $stats = (cyber_ext() && cyber_bool('reviews_enabled', true))
                        ? \Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews::stats($product->id)
                        : null;
                @endphp
                <div class="cyber-card cyber-card-hover cyber-clip flex flex-col overflow-hidden relative {{ $isSoldOut ? 'cyber-strike' : '' }} {{ $isPopular ? 'cyber-popular-card' : '' }}">
                    @if($isPopular)
                    <x-cyber.popular-badge />
                    @endif

                    @if ($product->image)
                    <x-cyber.picture :src="Storage::url($product->image)" :alt="$product->name"
                        :height="cyber_bool('small_images', false) ? 'h-24' : 'h-40'" max="max-h-[24rem]" />
                    @endif

                    <div class="p-4 flex flex-col flex-grow">
                        <h2 class="text-lg font-bold">{{ $product->name }}</h2>

                        {{-- Stock --}}
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            @if($product->stock === null)
                            <span class="inline-flex items-center gap-1 rounded-md border border-success/35 bg-success/10 px-2 py-0.5 text-success font-semibold">
                                <x-ri-infinity-fill class="size-3.5" /> Disponible
                            </span>
                            @elseif($isSoldOut)
                            <span class="inline-flex items-center gap-1 rounded-md border border-error/40 bg-error/10 px-2 py-0.5 text-error font-semibold">
                                <x-ri-close-circle-fill class="size-3.5" /> Agotado
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 rounded-md border border-success/35 bg-success/10 px-2 py-0.5 text-success font-semibold">
                                <x-ri-archive-2-fill class="size-3.5" /> {{ $product->stock }} en stock
                            </span>
                            @endif

                            @if($stats)
                            <span class="inline-flex items-center gap-1 text-base/55">
                                <x-ri-heart-3-fill class="size-3.5 text-primary" /> {{ $stats['likes'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-base/55">
                                <x-ri-chat-3-fill class="size-3.5 text-accent" /> {{ $stats['comments'] }}
                            </span>
                            @endif
                        </div>

                        @if(cyber_bool('direct_checkout', false) && $product->description)
                        <article class="prose prose-sm dark:prose-invert mt-3 text-base/65 line-clamp-3">
                            {!! $product->description !!}
                        </article>
                        @endif

                        <h3 class="mt-4 text-xl font-black cyber-gradient-text">
                            {{ $product->price()->formatted->price }}
                        </h3>

                        <div class="mt-auto pt-4 flex items-center gap-2">
                            @if(!$isSoldOut && $product->price()->available && cyber_bool('direct_checkout', false))
                            <a href="{{ route('products.checkout', ['category' => $product->category, 'product' => $product->slug]) }}"
                                wire:navigate class="flex-grow">
                                <x-button.primary class="w-full">
                                    {{ __('product.add_to_cart') }}
                                </x-button.primary>
                            </a>
                            @else
                            <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}"
                                wire:navigate class="flex-grow">
                                <x-button.primary class="w-full">
                                    {{ __('common.button.view') }}
                                </x-button.primary>
                            </a>
                            @if (!$isSoldOut && $product->price()->available)
                            <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}" wire:navigate>
                                <x-button.secondary class="!w-fit">
                                    <x-ri-shopping-bag-4-fill class="size-5" />
                                </x-button.secondary>
                            </a>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="cyber-card cyber-clip p-8 col-span-full text-center">
                    <x-ri-inbox-2-line class="size-10 mx-auto text-base/30" />
                    <p class="mt-3 text-base/60">No hay productos en esta categoría.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
