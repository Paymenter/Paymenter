@php
$isSoldOut = $product->stock !== null && $product->stock <= 0;
$reviewsOn = cyber_ext() && cyber_bool('reviews_enabled', true);
$popular = $reviewsOn ? \Paymenter\Extensions\Others\CyberpunkTheme\Support\Reviews::popularProductIds() : [];
$isPopular = in_array($product->id, $popular, true);
@endphp

<div class="container mt-10 pb-10">
    <div class="cyber-card cyber-clip p-6 md:p-8 relative overflow-hidden {{ $isPopular ? 'cyber-popular-card' : '' }}">
        <div class="absolute inset-0 cyber-gradient opacity-[0.06] pointer-events-none"></div>
        <div class="relative flex flex-col @if ($product->image) md:grid grid-cols-2 gap-10 @endif">
            @if ($product->image)
            <div class="relative rounded-xl overflow-hidden border border-neutral">
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                    class="w-full h-96 object-contain object-center bg-background/40">
            </div>
            @endif

            <div class="flex flex-col">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($isPopular)
                    <span class="cyber-popular inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-black uppercase tracking-wider text-white">
                        <x-ri-fire-fill class="size-4" /> Más popular
                    </span>
                    @endif
                    @if ($product->stock === null)
                    <span class="cyber-chip !border-success/45 !bg-success/12 !text-success">
                        <x-ri-infinity-fill class="size-3.5" /> {{ __('product.in_stock') }}
                    </span>
                    @elseif ($isSoldOut)
                    <span class="cyber-chip !border-error/45 !bg-error/12 !text-error">
                        <x-ri-close-circle-fill class="size-3.5" /> {{ __('product.out_of_stock', ['product' => $product->name]) }}
                    </span>
                    @else
                    <span class="cyber-chip !border-success/45 !bg-success/12 !text-success">
                        <x-ri-archive-2-fill class="size-3.5" /> {{ $product->stock }} unidades disponibles
                    </span>
                    @endif
                </div>

                <h1 class="text-3xl md:text-4xl font-black cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ $product->name }}">{{ $product->name }}</span>
                </h1>
                <h2 class="mt-2 text-2xl font-black cyber-gradient-text">
                    {{ $product->price()->formatted->price }}
                </h2>

                @if($product->stock !== null && $product->stock > 0)
                <div class="mt-4 max-w-xs">
                    <div class="cyber-meter">
                        <span style="width: {{ min(100, max(6, $product->stock * 10)) }}%"></span>
                    </div>
                    <p class="text-xs text-base/50 mt-1.5">Quedan {{ $product->stock }} unidades</p>
                </div>
                @endif

                <article class="my-6 prose dark:prose-invert max-w-none">
                    {!! $product->description !!}
                </article>

                @if (!$isSoldOut && $product->price()->available)
                <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}"
                    wire:navigate class="mt-auto">
                    <x-button.primary class="cyber-sweep py-3">
                        <x-ri-shopping-bag-4-fill class="size-5" />
                        {{ __('product.add_to_cart') }}
                    </x-button.primary>
                </a>
                @else
                <div class="mt-auto rounded-xl border border-error/40 bg-error/10 p-4 text-error text-sm font-semibold">
                    Este producto no está disponible en este momento.
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($reviewsOn)
    <div class="mt-8">
        <livewire:cyberpunk.product-reviews :product-id="$product->id" />
    </div>
    @endif
</div>
