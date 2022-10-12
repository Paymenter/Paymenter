<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- display all categories with products -->
                    <h1 class="text-2xl font-bold">{{ __('Categories') }}</h1>
                    @foreach ($categories as $category)
                        <div class="mt-4">
                            <h2 class="text-xl font-bold">{{ $category->name }}</h2>
                            <div class="flex flex-wrap">
                                @foreach ($category->products as $product)
                                    <div class="w-1/4 p-4">
                                        <a href="{{ route('products.show', $product->id) }}">
                                            <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                                class="w-full h-64 object-cover object-center">
                                            <div class="mt-2">
                                                <h3 class="text-lg font-medium text-gray-900">{{ $product->name }}</h3>
                                                <p class="mt-1 text-sm text-gray-500">{{ $product->description }}</p>
                                                <p class="mt-1 text-sm text-gray-500">${{ $product->price }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>