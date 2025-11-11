<div class="mb-12 text-center">
    <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $category->name }}</h1>
    @if(theme('show_category_description', true))
    <article class="prose dark:prose-invert mx-auto text-muted">
        {!! $category->description !!}
    </article>
    @endif
</div>

<div class="grid md:grid-cols-4 gap-6">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
            @foreach ($categories as $ccategory)
                <!-- List all categories simple under each other -->
                <a href="{{ route('category.show', ['category' => $ccategory->slug]) }}" wire:navigate
                    @if ($category->id == $ccategory->id) class="font-bold" @endif>
                    {{ $ccategory->name }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="flex flex-col gap-6 col-span-3">
        @if (count($childCategories) >= 1)
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 h-fit">
                @foreach ($childCategories as $childCategory)
                    <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
                        @if(theme('small_images', false))
                            <div class="flex gap-x-3 items-center">
                        @endif
                        @if ($childCategory->image)
                            <img src="{{ Storage::url($childCategory->image) }}" alt="{{ $childCategory->name }}"
                                class="rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
                        @endif
                        <h2 class="text-xl font-bold">{{ $childCategory->name }}</h2>
                        @if(theme('small_images', false))
                            </div>
                        @endif
                        @if(theme('show_category_description', true))
                            <article class="mt-2 prose dark:prose-invert">
                                {!! $childCategory->description !!}
                            </article>
                        @endif
                        <a href="{{ route('category.show', ['category' => $childCategory->slug]) }}" wire:navigate class="mt-2">
                            <x-button.primary>
                                {{ __('general.view') }}
                            </x-button.primary>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 h-fit">
            @foreach ($products as $product)
                <div class="kila-card flex flex-col bg-background-secondary border border-neutral p-6">
                    @if(theme('small_images', false))
                        <div class="flex gap-x-3 items-center">
                    @endif
                    @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                            class="rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
                    @endif
                    <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                    @if(theme('small_images', false))
                        </div>
                    @endif
                    @if(theme('direct_checkout', false) && $product->description) 
                        <article class="prose dark:prose-invert">
                            {!! $product->description !!}
                        </article>
                    @endif
                    <div class="mt-auto">
                        <h3 class="text-2xl font-bold mb-4">
                            {{ $product->price() }}
                        </h3>
                        @if (($product->stock > 0 || !$product->stock) && $product->price()->available && theme('direct_checkout', false))
                            <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}"
                                wire:navigate class="block">
                                <x-button.primary class="w-full btn-kila-success">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ __('product.add_to_cart') }}
                                </x-button.primary>
                            </a>
                        @else
                            <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}"
                                wire:navigate class="block">
                                <x-button.primary class="w-full">
                                    {{ __('general.view') }}
                                </x-button.primary>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
