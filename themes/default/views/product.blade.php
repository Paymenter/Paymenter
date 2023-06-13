<x-app-layout title="products">
    <script>
        function removeElement(element) {
            element.remove();
            this.error = true;
        }
    </script>
    <div class="content">
        <div class="grid grid-cols-12 gap-4">
            @if ($categories->count() > 0)
                <div class="lg:col-span-3 col-span-12">
                    <div class="content-box">
                        <div class="flex gap-x-2 items-center">
                            <div class="bg-primary-400 w-8 h-8 flex items-center justify-center rounded-md text-gray-50 text-xl">
                                <i class="ri-list-indefinite"></i>
                            </div>
                            <h3 class="font-semibold text-lg">{{ __("Categories") }}</h3>
                        </div>
                        <div class="flex flex-col gap-2 mt-2">
                            @foreach ($categories as $categoryItem)
                            @if ($categoryItem->products->count() > 0)
                            <a href="{{ route('products', $categoryItem->slug) }}" class="@if ($category->name == $categoryItem->name) text-secondary-900 pl-3 !border-primary-400 @endif border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ $categoryItem->name }}
                            </a>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="@if ($categories->count() > 0) lg:col-span-9 @endif col-span-12">
                <div class="content-box">
                    <h1 class="text-3xl font-semibold text-secondary-900">{{ $category->name }}</h1>
                    <p>{{ $category->description }}</p>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    @foreach ($category->products()->get() as $product)
                    <div class="md:col-span-1 col-span-3">
                        <div class="content-box h-full flex flex-col">
                            <div class="flex gap-x-3 items-center mb-2">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-14 rounded-md"  onerror="removeElement(this);">
                                <div>
                                    <h3 class="text-lg text-secondary-800 leading-5 font-semibold">{{ $product->name }}</h3>
                                    <p>{{ $product->price() ? config('settings::currency_sign') . $product->price() : __('Free') }}</p>
                                </div>
                            </div>
                            <p>{{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', $product->description)) }}</p>
                            <div class="pt-3 mt-auto">
                                <a href="{{ route('checkout.add', $product->id) }}" class="button button-secondary w-full">Add to cart <i class="ri-shopping-cart-2-line"></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>