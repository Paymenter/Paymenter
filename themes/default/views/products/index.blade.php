<div class="container mt-14">
    <div class="flex flex-col md:grid md:grid-cols-4 gap-4">
        <div class="flex flex-col gap-2">
            <div class="mx-auto container">
                <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
                <article class="prose dark:prose-invert">
                    {!! $category->description !!}
                </article>
            </div>
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
                            class="aspect-square rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
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
                            {{ __('common.button.view') }}
                        </x-button.primary>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 h-fit">
                @foreach ($products as $product)
                <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
                    @if(theme('small_images', false))
                    <div class="flex gap-x-3 items-center">
                        @endif
                        @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                            class="aspect-square rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
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
                    <h3 class="text-lg font-semibold mb-2">
                        {{ $product->price()->formatted->price }}
                    </h3>
                    <div class="mt-auto pt-2 flex items-center gap-2">
                        @if($product->stock !== 0 && $product->price()->available && theme('direct_checkout', false))
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
                        @if ($product->stock !== 0 && $product->price()->available)
                        <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}"
                            wire:navigate>
                            <x-button.secondary>
                                <x-ri-shopping-bag-4-fill class="size-6" />
                            </x-button.secondary>
                        </a>
                        @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>