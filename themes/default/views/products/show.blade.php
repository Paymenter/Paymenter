<div class="container mt-14">
    <div class="flex flex-col @if ($product->image) md:grid grid-cols-2 gap-16 bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg @endif">
        @if ($product->image)
        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
            class="w-full h-96 object-contain object-center rounded-md">
        @endif
        {{-- If your happiness depends on money, you will never be happy with yourself. --}}
        <div class="flex flex-col">
            @if ($product->stock === 0)
            <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded bg-red-900 text-red-300 w-fit mb-3">
                {{ __('product.out_of_stock', ['product' => $product->name]) }}
            </span>
            @elseif($product->stock > 0)
            <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded bg-green-900 text-green-300 w-fit mb-3">
                {{ __('product.in_stock') }}
            </span>
            @endif
            <div class="flex flex-row justify-between">
                <div>
                    <h2 class="text-3xl font-bold">{{ $product->name }}</h2>
                    <h3 class="text-xl font-semibold">
                        {{ $product->price()->formatted->price }}
                    </h3>
                </div>
                @if ($product->stock !== 0 && $product->price()->available)
                <div>
                    <x-button.secondary>
                        <x-ri-shopping-bag-4-fill class="size-6" />
                    </x-button.secondary>
                </div>
                @endif
            </div>
            <article class="my-4 prose dark:prose-invert">
                {!! $product->description !!}
            </article>

            @if ($product->stock !== 0 && $product->price()->available)
            <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}"
                wire:navigate>
                <x-button.primary>{{ __('product.add_to_cart') }}</x-button.primary>
            </a>
            @endif
        </div>
    </div>
</div>