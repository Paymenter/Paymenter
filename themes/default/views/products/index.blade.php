<div class="grid md:grid-cols-4 gap-4">
    <div class="flex flex-col gap-2">
        <div class="mx-auto container bg-primary-800 p-4 rounded-md">
            <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
            <article class="prose prose-invert">
                {!! Str::markdown($category->description, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                ]) !!}
            </article>
        </div>
        <div class="flex flex-col bg-primary-800 p-4 rounded-md mb-4">
            @foreach ($categories as $ccategory)
                <!-- List all categories simple under each other -->
                <a href="{{ route('category.show', ['category' => $ccategory->slug]) }}" wire:navigate @if($ccategory->id == $category->id) class="font-bold" @endif>
                    {{ $ccategory->name }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="flex flex-col gap-6 col-span-3">
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 h-fit">
            @foreach($childCategories as $ccategory)
                <div class="flex flex-col bg-primary-800 p-4 rounded-md">
                    @if ($ccategory->image)
                        <img src="{{ url()->to($ccategory->image) }}" alt="{{ $ccategory->name }}"
                            class="w-full object-cover object-center rounded-md">
                    @endif
                    <h2 class="text-xl font-bold mb-2">{{ $ccategory->name }}</h2>
                    <a href="{{ route('category.show', ['category' => $ccategory->slug]) }}" wire:navigate>
                        <x-button.primary>
                            {{ __('general.view') }}
                        </x-button.primary>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 h-fit">
            @foreach($products as $product)
                <div class="flex flex-col bg-primary-800 p-4 rounded-md">
                    @if ($product->image)
                        <img src="{{ url()->to($product->image) }}" alt="{{ $product->name }}"
                            class="w-full object-cover object-center rounded-md">
                    @endif
                    <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                    <h3 class="text-lg font-semibold mb-2">
                        {{ $product->price() }}
                    </h3>
                    <a href="{{ route('products.show', ['category' => $product->category, 'product' => $product->slug]) }}" wire:navigate>
                        <x-button.primary>
                            {{ __('general.view') }}
                        </x-button.primary>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>