<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col lg:grid lg:grid-cols-4 gap-6 lg:gap-8">
        
        {{-- Sidebar --}}
        <aside class="flex flex-col gap-6 lg:sticky lg:top-20 lg:h-[calc(100vh-5rem)] lg:overflow-y-auto">
            {{-- Category Header --}}
            <div class="px-2">
                <h1 class="text-3xl sm:text-4xl font-black uppercase tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-3">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                    <article class="prose prose-sm dark:prose-invert text-gray-600 dark:text-gray-400 leading-relaxed">
                        {!! $category->description !!}
                    </article>
                @endif
            </div>

            {{-- Categories Navigation --}}
            <nav class="flex flex-col bg-white/50 dark:bg-gray-900/50 backdrop-blur-xl border border-gray-200 dark:border-gray-800 rounded-2xl p-2 shadow-lg">
                <p class="px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
                    📂 Categories
                </p>
                @foreach ($categories as $ccategory)
                    <a href="{{ route('category.show', ['category' => $ccategory->slug]) }}" 
                       wire:navigate
                       class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 
                              {{ $category->id == $ccategory->id 
                                 ? 'bg-primary-50 dark:bg-primary-950/30 text-primary-700 dark:text-primary-400 border border-primary-200 dark:border-primary-800' 
                                 : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                        <span class="text-xs font-bold uppercase tracking-wider">{{ $ccategory->name }}</span>
                        @if($category->id == $ccategory->id)
                            <div class="size-1.5 bg-primary-500 rounded-full shadow-[0_0_8px_rgba(var(--primary-rgb),0.8)] animate-pulse"></div>
                        @else
                            <x-ri-arrow-right-s-line class="size-4 opacity-0 group-hover:opacity-100 transition-all duration-200 transform group-hover:translate-x-1" />
                        @endif
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="flex flex-col gap-10 col-span-1 lg:col-span-3">
            
            {{-- Subcategories Section --}}
            @if (count($childCategories) >= 1)
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            📁 Subcategories
                        </h2>
                        <div class="h-px flex-1 ml-4 bg-gradient-to-r from-gray-200 dark:from-gray-800 to-transparent"></div>
                    </div>
                    
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($childCategories as $childCategory)
                            <div class="group relative flex flex-col bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-5 rounded-2xl transition-all duration-300 hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-700 hover:-translate-y-1">
                                
                                {{-- Category Image --}}
                                @if ($childCategory->image)
                                    <div class="overflow-hidden rounded-xl mb-4">
                                        <img src="{{ Storage::url($childCategory->image) }}" alt="{{ $childCategory->name }}"
                                             class="aspect-video w-full object-cover transition-transform duration-500 group-hover:scale-110 {{ theme('small_images', false) ? 'max-w-[80px]' : '' }}">
                                    </div>
                                @endif
                                
                                <div class="flex {{ theme('small_images', false) ? 'flex-row items-center gap-4' : 'flex-col gap-3' }}">
                                    <h3 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white line-clamp-1">
                                        {{ $childCategory->name }}
                                    </h3>
                                </div>
                                
                                @if(theme('show_category_description', true) && $childCategory->description)
                                    <article class="mt-2 prose prose-sm dark:prose-invert text-gray-500 dark:text-gray-400 line-clamp-2 text-sm">
                                        {!! Str::limit($childCategory->description, 100) !!}
                                    </article>
                                @endif

                                <div class="mt-4 pt-2">
                                    <a href="{{ route('category.show', ['category' => $childCategory->slug]) }}" wire:navigate>
                                        <x-button.secondary class="w-full py-2.5 text-xs font-semibold rounded-xl">
                                            Browse Category →
                                        </x-button.secondary>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                
                @if(count($products) > 0)
                    <div class="border-t border-gray-200 dark:border-gray-800 my-4"></div>
                @endif
            @endif

            {{-- Products Section --}}
            @if(count($products) > 0)
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            🛍️ Products 
                            @if(method_exists($products, 'total'))
                                ({{ $products->total() }})
                            @else
                                ({{ $products->count() }})
                            @endif
                        </h2>
                        <div class="h-px flex-1 ml-4 bg-gradient-to-r from-gray-200 dark:from-gray-800 to-transparent"></div>
                    </div>
                    
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($products as $product)
                            <div class="group flex flex-col bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-700 hover:-translate-y-1">
                                
                                {{-- Product Image --}}
                                <div class="relative overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                                    @if ($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                             class="aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    @else
                                        <div class="aspect-[4/3] w-full flex items-center justify-center">
                                            <x-ri-shopping-bag-4-line class="size-12 text-gray-400 dark:text-gray-600" />
                                        </div>
                                    @endif
                                    
                                    {{-- Price Badge --}}
                                    <div class="absolute top-3 right-3 px-3 py-1.5 bg-black/70 dark:bg-black/80 backdrop-blur-md rounded-full border border-white/10 shadow-lg">
                                        <span class="text-xs font-black text-primary-400 dark:text-primary-400 uppercase tracking-wider">
                                            {{ $product->price()->formatted->price }}
                                        </span>
                                    </div>
                                    
                                    {{-- Stock Badge --}}
                                    @if($product->stock === 0)
                                        <div class="absolute bottom-3 left-3 px-2 py-1 bg-red-500/90 backdrop-blur-sm rounded-md">
                                            <span class="text-[9px] font-black text-white uppercase tracking-wider">Out of Stock</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="text-base font-bold tracking-tight text-gray-900 dark:text-white line-clamp-1 mb-1">
                                        {{ $product->name }}
                                    </h3>
                                    
                                    @if(theme('direct_checkout', false) && $product->description)
                                        <article class="prose prose-sm dark:prose-invert text-gray-500 dark:text-gray-400 line-clamp-2 mb-3 text-xs">
                                            {!! Str::limit($product->description, 100) !!}
                                        </article>
                                    @endif

                                    <div class="mt-auto pt-3 flex items-center gap-2">
                                        @if($product->stock !== 0 && $product->price()->available && theme('direct_checkout', false))
                                            <a href="{{ route('products.checkout', ['category' => $product->category, 'product' => $product->slug]) }}"
                                               wire:navigate class="flex-1">
                                                <x-button.primary class="w-full py-2.5 text-xs font-semibold rounded-xl">
                                                    <x-ri-shopping-cart-line class="size-4 mr-1.5" />
                                                    Add to Cart
                                                </x-button.primary>
                                            </a>
                                        @else
                                            <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}"
                                               wire:navigate class="flex-1">
                                                <x-button.primary class="w-full py-2.5 text-xs font-semibold rounded-xl">
                                                    View Details
                                                </x-button.primary>
                                            </a>
                                            @if ($product->stock !== 0 && $product->price()->available)
                                                <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}" 
                                                   wire:navigate>
                                                    <x-button.secondary class="px-4 py-2.5 rounded-xl">
                                                        <x-ri-shopping-bag-4-fill class="size-4" />
                                                    </x-button.secondary>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    @if(method_exists($products, 'links') && $products->hasPages())
                        <div class="mt-10">
                            {{ $products->links() }}
                        </div>
                    @endif
                </section>
            @else
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-24 h-24 mb-6 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                        <x-ri-inbox-line class="size-12 text-gray-400 dark:text-gray-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Products Found</h3>
                    <p class="text-gray-500 dark:text-gray-400">There are no products available in this category yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>