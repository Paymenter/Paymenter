<x-app-layout clients title="{{ __('Product') }} {{ $product->name }}">
    <script>
        function removeElement(element) {
            element.remove();
            this.error = true;
        }
    </script>
    <div class="content">
        <div class="content-box">
            <h1 class="text-2xl font-semibold text-secondary-900">Upgrading {{ $product->name }}</h1>
            @if ($product->image !== 'null')
                <img src="{{ $product->image }}" class="w-20 h-full rounded-md mr-4" onerror="removeElement(this);">
            @endif
            <div class="prose dark:prose-invert">
                @markdownify($product->description)
            </div>
            <p class="mt-4">
               Current Price: <x-money :amount="$orderProduct->price" />
            </p>
        </div>

        <div class="mt-4 content-box">
            <h2 class="text-xl font-semibold text-secondary-900">Choose a product to upgrade to</h2>
            <div class="grid grid-cols-4 gap-4 mt-4">
                @foreach ($orderProduct->availableUpgrades() as $product2)
                    @if ($product2->id == $product->id)
                        @continue
                    @endif
                    <div class="md:col-span-2 lg:col-span-1 col-span-4">
                        <div class="content-box h-full flex flex-col bg-secondary-200 dark:bg-secondary-200">
                            <div class="flex gap-x-3 items-center mb-2">
                                @if ($product2->image !== 'null')
                                    <img src="{{ $product2->image }}" alt="{{ $product2->name }}" class="w-14 rounded-md"
                                        onerror="removeElement(this);">
                                @endif
                                <div>
                                    <h3 class="text-lg text-secondary-800 leading-5 font-semibold">
                                        {{ $product2->name }}</h3>
                                    <p>
                                        <x-money :amount="$product2->price()" showFree="true" />
                                    </p>
                                </div>
                            </div>
                            <div class="prose dark:prose-invert">
                                @markdownify($product2->description)
                            </div>
                            <div class="pt-3 mt-auto">
                                @if ($product2->stock_enabled && $product2->stock <= 0)
                                    <a class="button bg-secondary-200 text-white w-full hover:cursor-not-allowed">
                                        Out of stock
                                    </a>
                                @else
                                    <a class="button button-secondary bg-secondary-300 w-full" href="{{ route('clients.active-products.upgrade-product',[$orderProduct, $product2->id]) }}">
                                        Upgrade <i class="ri-shopping-cart-2-line"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>



</x-app-layout>