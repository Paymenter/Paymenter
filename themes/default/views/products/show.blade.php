<div class="flex flex-col @if ($product->image) md:grid grid-cols-2 gap-16 @endif">
    @if ($product->image)
        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
            class="w-full h-96 object-cover object-center rounded-md">
    @endif
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="flex flex-col">
        @if ($product->stock === 0)
            <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded bg-red-900 text-red-300 w-fit mb-3">
                {{ __('product.out_of_stock') }}
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
                    {{ $product->price() }}
                </h3>
            </div>
            @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
                <div>
                    <x-button.secondary>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </x-button.secondary>
                </div>
            @endif
        </div>
        <article class="my-4 prose prose-invert">
            {!! $product->description !!}
        </article>

        @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
            <a href="{{ route('products.checkout', ['category' => $category, 'product' => $product->slug]) }}"
                wire:navigate>
                <x-button.primary>{{ __('product.add_to_cart') }}</x-button.primary>
            </a>
        @endif
    </div>
</div>
