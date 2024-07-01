<div class="grid grid-cols-4 gap-4">
    <div class="flex flex-col gap-4 col-span-3">
        @if ($items->isEmpty())
            <h1 class="text-2xl font-semibold">
                {{ __('product.empty_cart') }}
            </h1>
        @endif
        @foreach ($items as $key => $item)
            <div class="flex flex-col gap-4">
                <h2 class="text-2xl font-semibold">
                    {{ $item->product->name }}
                </h2>
                <h3 class="text-xl font-semibold">
                    {{ $item->price }}
                </h3>
                <div class="flex flex-row gap-4">
                    <x-button.primary wire:click="testsmth">
                        {{ __('product.checkout') }}
                    </x-button.primary>
                    <x-button.secondary wire:click="removeProduct({{ $key }})">
                        {{ __('product.remove') }}
                    </x-button.secondary>
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex flex-col gap-4 w-full col-span-1">
        @if (!$items->isEmpty())
            <h2 class="text-2xl font-semibold">
                {{ __('product.price') }}
            </h2>
            <h3 class="text-xl font-semibold">
                {{ $total }}
            </h3>
        @endif
    </div>
</div>
