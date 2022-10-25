<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
<!-- dispaly company name in full width -->
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="dark:bg-darkmode2 bg-white overflow-hidden shadow-xl rounded-lg">
            <div class="dark:bg-darkmode2 p-6 sm:px-20 bg-white">
                <div class="mt-8 text-4xl text-blue-500 text-center">
                    {{ config('app.name', 'Paymenter') }}
                </div>
                <div class="dark:text-darkmodetext mt-6 text-gray-500 text-xl text-center">
                    {{ __('Thanks for using Paymenter!') }}
                </div>
                <!-- you may want to change this -->
                <div class="dark:text-darkmodetext mt-6 text-gray-500 text-xl text-center">
                    {{ __('This is the default theme. You can change it in the settings. Or download one on our marketplace ') }}
                </div>
            </div>
        </div>
    </div>
    

    <div class="dark:bg-darkmode dark:text-darkmodetext py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white rounded-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
                    <!-- display all categories with products -->
                    <h1 class="text-center text-2xl font-bold">{{ __('Categories') }}</h1>
                    @foreach ($categories as $category)
                        <div class="mt-4">
                            <h2 class="text-center text-xl font-bold">{{ $category->name }}</h2>
                            <div class="flex flex-nowrap gap-4">
                                @foreach ($category->products as $product)
                                    <div class="transition delay-400 hover:shadow-lg dark:bg-darkmode rounded-lg w-1/4 p-4">
                                        <a href="{{ route('checkout.add') }}?id={{  $product->id }}">
                                            <img class="rounded-lg" src="{{ $product->image }}" alt="{{ $product->name }}"
                                                class="w-full h-64 object-cover object-center">
                                            <div class="mt-2">
                                                <h3 class="text-center dark:text-darkmodetext text-lg font-medium text-gray-900">{{ $product->name }}</h3>
                                                <p class="text-center dark:text-darkmodetext mt-1 text-sm text-gray-500">{{ $product->description }}</p>
                                                <p class="text-center dark:text-darkmodetext mt-1 text-sm text-gray-500">${{ $product->price }}</p>
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