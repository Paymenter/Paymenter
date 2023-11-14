<div class="md:col-span-1 col-span-3">
    <div class="content-box h-full flex flex-col">
        <div class="flex gap-x-3 items-center mb-2">
            @if ($product->image !== 'null')
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-14 rounded-md"
                    onerror="removeElement(this);">
            @endif
            <div>
                <h3 class="text-lg text-secondary-800 leading-5 font-semibold">
                    {{ $product->name }}</h3>
                <p>
                    <x-money :amount="$product->price()" showFree="true" />
                </p>
            </div>
        </div>
        <div class="prose dark:prose-invert">
            @markdownify($product->description)
        </div>
        <div class="pt-3 mt-auto">
            @if ($product->stock_enabled && $product->stock <= 0)
                <a class="button bg-secondary-200 text-white w-full hover:cursor-not-allowed">
                    Out of stock
                </a>
            @else
                <button class="button button-secondary w-full @if ($added) !text-green-400 @endif"
                    wire:click="addToCart">
                    @if ($added)
                        Added to cart <i class="ri-check-line"></i>
                    @else
                        Add to cart <i class="ri-shopping-cart-2-line"></i>
                    @endif
                </button>
            @endif
        </div>
    </div>
</div>
