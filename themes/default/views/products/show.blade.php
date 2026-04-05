<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in slide-in-from-bottom-6 duration-1000">
    <div class="flex flex-col @if ($product->image) lg:grid lg:grid-cols-2 gap-8 lg:gap-16 xl:gap-20 @endif">
        
        {{-- Product Image Section --}}
        @if ($product->image)
        <div class="relative group order-1 lg:order-none">
            {{-- Background Glow Effect --}}
            <div class="absolute -inset-4 bg-primary-500/10 dark:bg-primary-400/10 blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
            
            {{-- Image Container --}}
            <div class="relative bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 backdrop-blur-sm border border-gray-200 dark:border-gray-800 p-6 md:p-8 rounded-3xl md:rounded-[3rem] overflow-hidden shadow-2xl">
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                    class="w-full h-auto max-h-[400px] md:max-h-[500px] object-contain object-center scale-95 group-hover:scale-100 transition-transform duration-700 ease-out">
                
                {{-- Image Overlay Gradient --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            </div>
            
            {{-- Image Badges --}}
            @if(isset($product->featured) && $product->featured)
            <div class="absolute top-4 left-4 z-10">
                <span class="px-3 py-1.5 bg-amber-500 text-white text-[10px] font-black uppercase tracking-wider rounded-full shadow-lg">
                    ⭐ Featured
                </span>
            </div>
            @endif
        </div>
        @endif

        {{-- Product Info Section --}}
        <div class="flex flex-col justify-center py-4 md:py-6 order-2">
            
            {{-- Stock Status Badge --}}
            <div class="mb-6 flex flex-wrap items-center gap-3">
                @if ($product->stock === 0)
                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-red-500/10 dark:bg-red-500/20 border border-red-500/30 text-[10px] font-black uppercase tracking-[0.2em] text-red-600 dark:text-red-400">
                    <span class="size-1.5 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                    {{ __('product.out_of_stock', ['product' => $product->name]) ?: 'Out of Stock' }}
                </span>
                @elseif($product->stock > 0 && $product->stock < 10)
                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-orange-500/10 dark:bg-orange-500/20 border border-orange-500/30 text-[10px] font-black uppercase tracking-[0.2em] text-orange-600 dark:text-orange-400">
                    <span class="size-1.5 bg-orange-500 rounded-full mr-2 animate-pulse"></span>
                    Only {{ $product->stock }} Left in Stock
                </span>
                @elseif($product->stock > 0)
                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/30 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">
                    <span class="size-1.5 bg-emerald-500 rounded-full mr-2 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                    {{ __('product.in_stock') ?: 'In Stock' }}
                </span>
                @endif
                
                {{-- SKU Badge --}}
                @if(isset($product->sku) && $product->sku)
                <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-gray-500/10 dark:bg-gray-500/20 border border-gray-500/30 text-[9px] font-mono uppercase tracking-wider text-gray-600 dark:text-gray-400">
                    SKU: {{ $product->sku }}
                </span>
                @endif
            </div>

            {{-- Product Title & Price --}}
            <div class="space-y-3 mb-6 md:mb-8">
                {{-- Breadcrumb Trail --}}
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-2">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-primary-600 dark:hover:text-primary-400 transition">Home</a>
                    <x-ri-arrow-right-s-line class="size-3 flex-shrink-0" />
                    <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="hover:text-primary-600 dark:hover:text-primary-400 transition">
                        {{ $category->name }}
                    </a>
                    <x-ri-arrow-right-s-line class="size-3 flex-shrink-0" />
                    <span class="text-gray-900 dark:text-white font-medium truncate">{{ $product->name }}</span>
                </div>
                
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent leading-tight">
                    {{ $product->name }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl md:text-4xl font-black text-primary-600 dark:text-primary-400 tracking-tight">
                            {{ $product->price()->formatted->price }}
                        </span>
                        @php
                            $formattedPrice = $product->price()->formatted;
                        @endphp
                        @if(isset($formattedPrice->compare_at_price) && $formattedPrice->compare_at_price)
                        <span class="text-lg text-gray-400 dark:text-gray-500 line-through">
                            {{ $formattedPrice->compare_at_price }}
                        </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500 italic">Global Pricing</span>
                </div>
            </div>

            {{-- Product Description --}}
            @if($product->description)
            <article class="prose prose-sm md:prose-base dark:prose-invert text-gray-600 dark:text-gray-400 leading-relaxed border-l-2 border-gray-200 dark:border-gray-800 pl-4 md:pl-6 my-6 md:my-8">
                {!! $product->description !!}
            </article>
            @endif

            {{-- Product Features (Optional) --}}
            @if(isset($product->features) && is_array($product->features) && count($product->features) > 0)
            <div class="my-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                    Key Features
                </h3>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($product->features as $feature)
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-ri-checkbox-circle-fill class="size-4 text-emerald-500" />
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Action Buttons --}}
            @if ($product->stock !== 0 && $product->price()->available)
            <div class="flex flex-col sm:flex-row items-stretch gap-4 mt-4 md:mt-6">
                <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}"
                    wire:navigate class="flex-1 group">
                    <x-button.primary class="w-full py-4 md:py-5 text-sm font-bold rounded-xl md:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 group-hover:scale-[1.02]">
                        <x-ri-shopping-cart-line class="size-5 mr-2 group-hover:scale-110 transition-transform" />
                        {{ __('product.add_to_cart') ?: 'Add to Cart' }}
                    </x-button.primary>
                </a>
                
                {{-- Wishlist Button (Optional) --}}
                <button 
                    @click="wishlist = !wishlist" 
                    x-data="{ wishlist: false }"
                    class="flex items-center justify-center px-5 py-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-300 group">
                    <x-ri-heart-3-line x-show="!wishlist" class="size-5 text-gray-600 dark:text-gray-400 group-hover:text-red-500 transition-colors" />
                    <x-ri-heart-3-fill x-show="wishlist" class="size-5 text-red-500" x-cloak />
                </button>
            </div>
            
            {{-- Trust Badges --}}
            <div class="mt-6 pt-4 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-[9px] font-black uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500">
                <span class="flex items-center gap-1.5">
                    <x-ri-shield-check-line class="size-3" />
                    Secure Payment
                </span>
                <span class="w-px h-3 bg-gray-300 dark:bg-gray-700"></span>
                <span class="flex items-center gap-1.5">
                    <x-ri-flashlight-line class="size-3" />
                    Instant Delivery
                </span>
                <span class="w-px h-3 bg-gray-300 dark:bg-gray-700"></span>
                <span class="flex items-center gap-1.5">
                    <x-ri-customer-service-line class="size-3" />
                    24/7 Support
                </span>
            </div>
            @endif
        </div>
    </div>
    
    {{-- Related Products Section (Optional) --}}
    @if(isset($relatedProducts) && count($relatedProducts) > 0)
    <div class="mt-16 md:mt-20 pt-8 border-t border-gray-200 dark:border-gray-800">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                You Might Also Like
            </h2>
            <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                View All →
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($relatedProducts as $relatedProduct)
            <div class="group bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                @if($relatedProduct->image)
                <div class="overflow-hidden">
                    <img src="{{ Storage::url($relatedProduct->image) }}" alt="{{ $relatedProduct->name }}"
                         class="w-full aspect-[4/3] object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                @endif
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 dark:text-white line-clamp-1 mb-1">
                        {{ $relatedProduct->name }}
                    </h3>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                            {{ $relatedProduct->price()->formatted->price }}
                        </span>
                        <a href="{{ route('products.show', ['category' => $category, 'product' => $relatedProduct->slug]) }}" 
                           wire:navigate
                           class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>