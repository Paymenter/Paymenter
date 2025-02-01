<div class="grid md:grid-cols-4 gap-4">
    <div class="flex flex-col gap-2">
        <div class="mx-auto container">
            <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
            <article class="prose dark:prose-invert">
                {!! Str::markdown($category->description, [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]) !!}
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
                        @if ($childCategory->image)
                            <img src="{{ Storage::url($childCategory->image) }}" alt="{{ $childCategory->name }}"
                                class="w-full object-cover object-center rounded-md">
                        @endif
                        <h2 class="text-xl font-bold mb-2">{{ $childCategory->name }}</h2>
                        <a href="{{ route('category.show', ['category' => $childCategory->slug]) }}" wire:navigate>
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
                <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
                    @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                            class="w-full object-cover object-center rounded-md">
                    @endif
                    <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                    @if(theme('direct_checkout', false) && $product->description) 
                        <article class="prose dark:prose-invert">
                            {!! $product->description !!}
                        </article>
                    @endif
                    <h3 class="text-lg font-semibold mb-2">
                        {{ $product->price() }}
                    </h3>
                    @if (($product->stock > 0 || !$product->stock) && $product->price()->available && theme('direct_checkout', false))
                        <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}"
                            wire:navigate>
                            <x-button.primary>{{ __('product.add_to_cart') }}</x-button.primary>
                        </a>
                    @else
                        <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}"
                            wire:navigate>
                            <x-button.primary>
                                {{ __('general.view') }}
                            </x-button.primary>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
