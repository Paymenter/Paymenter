@php $popularCategoryId = cyber_popular_category(); @endphp
<div>
    @if(cyber_bool('banner_enabled', true))
    <x-cyber.banner />
    @endif

    @if(cyber_bool('marquee_enabled', false))
    <x-cyber.marquee />
    @endif

    @if(cyber_bool('stats_enabled', true))
    <x-cyber.stats />
    @endif

    @if(cyber_bool('marketing_enabled', true))
    <x-cyber.marketing />
    @endif

    @if(cyber_bool('quick_links_enabled', true))
    <x-cyber.quick-links />
    @endif

    @if(trim((string) theme('home_page_text', '')) !== '')
    <section class="container py-6">
        <div class="cyber-card cyber-clip p-8">
            <article class="prose dark:prose-invert max-w-full">
                {!! Str::markdown(theme('home_page_text', ''), [
                'allow_unsafe_links' => false,
                'renderer' => ['soft_break' => "<br>"]
                ]) !!}
            </article>
        </div>
    </section>
    @endif

    {{-- Catálogo --}}
    <section class="container py-12" id="servicios">
        <x-cyber.section-title
            title="Nuestros servicios"
            subtitle="Elige la categoría que necesitas y revisa la disponibilidad en tiempo real."
            icon="ri-server-fill" />

        <div class="grid gap-5 mt-8 {{ cyber_cols(count($categories)) }}">
            @forelse ($categories as $category)
            @php
                $isPopularCategory = $popularCategoryId !== null && (int) $category->id === $popularCategoryId;
                $visibleProducts = $category->products->where('hidden', false);
                $totalProducts = $visibleProducts->count();
                $limited = $visibleProducts->whereNotNull('stock');
                $stockTotal = $limited->sum('stock');
                $soldOut = $visibleProducts->filter(fn ($p) => $p->stock !== null && $p->stock <= 0)->count();
                $allSoldOut = $totalProducts > 0 && $soldOut === $totalProducts;
            @endphp
            <div class="cyber-card cyber-card-hover cyber-clip flex flex-col overflow-hidden relative {{ $allSoldOut ? 'cyber-strike' : '' }} {{ $isPopularCategory ? 'cyber-popular-card' : '' }}">
                @if($isPopularCategory)
                <x-cyber.popular-badge label="LA MÁS POPULAR" />
                @endif
                @if ($category->image)
                <x-cyber.picture :src="Storage::url($category->image)" :alt="$category->name"
                    :height="cyber_bool('small_images', false) ? 'h-24' : 'h-40'" max="max-h-[26rem]" />
                @endif

                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-lg font-bold">{{ $category->name }}</h3>
                        @if($allSoldOut)
                        <span class="cyber-chip !border-error/50 !bg-error/15 !text-error">Agotado</span>
                        @endif
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-neutral bg-background/50 px-2 py-1">
                            <x-ri-stack-fill class="size-3.5 text-primary" />
                            {{ $totalProducts }} {{ $totalProducts === 1 ? 'producto' : 'productos' }}
                        </span>
                        @if($limited->count() > 0)
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-neutral bg-background/50 px-2 py-1">
                            <x-ri-archive-2-fill class="size-3.5 {{ $stockTotal > 0 ? 'text-success' : 'text-error' }}" />
                            {{ $stockTotal }} en stock
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-neutral bg-background/50 px-2 py-1">
                            <x-ri-infinity-fill class="size-3.5 text-success" />
                            Stock ilimitado
                        </span>
                        @endif
                    </div>

                    @if(cyber_bool('show_category_description', true) && $category->description)
                    <article class="prose prose-sm dark:prose-invert mt-3 text-base/70 line-clamp-3">
                        {!! $category->description !!}
                    </article>
                    @endif

                    <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="mt-auto pt-5">
                        <x-button.primary>
                            {{ __('common.button.view_all') }}
                            <x-ri-arrow-right-fill class="size-5" />
                        </x-button.primary>
                    </a>
                </div>
            </div>
            @empty
            <div class="cyber-card cyber-clip p-8 col-span-full text-center">
                <x-ri-inbox-2-line class="size-10 mx-auto text-base/30" />
                <p class="mt-3 text-base/60">Todavía no hay productos publicados.</p>
            </div>
            @endforelse
        </div>
    </section>

    {{-- Reseñas destacadas: después de los planes y antes de la comunidad --}}
    @if(cyber_ext() && cyber_bool('featured_reviews_enabled', true) && cyber_bool('reviews_enabled', true))
    <x-cyber.featured-reviews />
    @endif

    {{-- Comunidad --}}
    @if(cyber_ext() && cyber_bool('community_enabled', true))
    <livewire:cyberpunk.community-preview />
    @endif

    @if(cyber_bool('socials_enabled', true))
    <x-cyber.socials />
    @endif

    {!! hook('pages.home') !!}
</div>
