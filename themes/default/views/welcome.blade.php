<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>

    <div class="dark:bg-darkmode dark:text-darkmodetext py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white rounded-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
                    <!-- display all categories with products -->
                    <h1 class="text-2xl font-bold">{{ __('Categories') }}</h1>
                    @foreach ($categories as $category)
                        <div class="mt-4">
                            <h2 class="text-xl font-bold">{{ $category->name }}</h2>
                            <div class="flex flex-wrap">
                                @foreach ($category->products as $product)
                                    <div class="w-1/4 p-4">
                                        <a href="{{ route('checkout.add', $product->id) }}">
                                            <img class="rounded-lg" src="{{ $product->image }}" alt="{{ $product->name }}"
                                                class="w-full h-64 object-cover object-center">
                                            <div class="mt-2">
                                                <h3 class="dark:text-darkmodetext text-lg font-medium text-gray-900">{{ $product->name }}</h3>
                                                <p class="dark:text-darkmodetext mt-1 text-sm text-gray-500">{{ $product->description }}</p>
                                                <p class="dark:text-darkmodetext mt-1 text-sm text-gray-500">${{ $product->price }}</p>
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