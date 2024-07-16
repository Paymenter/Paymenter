<div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($products as $product)
        <div class="flex flex-col bg-primary-700 p-4 rounded-md mb-4">
            @if ($product->image)
                <img src="{{ url()->to($product->image) }}" alt="{{ $product->name }}"
                    class="w-full object-cover object-center rounded-md">
            @endif
            <h2 class="text-xl font-bold">{{ $product->name }}</h2>
            <h3 class="text-lg font-semibold">
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
