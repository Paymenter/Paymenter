<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
    <section class="bg-white dark:bg-gray-900">
        <div class="container px-6 py-10 mx-auto mt-16">
            @foreach ($categories as $category)
                <div class="mt-8 xl:mt-12">
                    <h2 class="text-xl font-bold text-center">{{ $category->name }}</h2>
                    <div class="grid grid-cols-1 gap-8 xl:gap-12 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($category->products as $product)
                            <div class="border-gray-500">
                                <div class="shadow-2xl pb-4 pt-2 pl-4 pr-4 bg-gray-100 rounded-b-lg shadow-slate-400">
                                    <img class="rounded-t-lg h-96 w-full" src="{{ $product->image }}"
                                        alt="{{ $product->name }}" onerror="removeElement(this);">
                                    <h2
                                        class="mt-4 text-2xl font-semibold text-center text-gray-800 capitalize dark:text-dark">
                                        {{ $product->name }}</h2>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('checkout.add') }}?id={{ $product->id }}"
                                            class="relative inline-flex items-center justify-center p-4 px-5 py-3 overflow-hidden font-medium text-indigo-600 transition duration-300 ease-out rounded-full shadow-xl group hover:ring-1 hover:ring-purple-500 text-center">
                                            <span
                                                class="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-600 via-purple-600 to-pink-700"></span>
                                            <span
                                                class="absolute bottom-0 right-0 block w-64 h-64 mb-32 mr-4 transition duration-500 origin-bottom-left transform rotate-45 translate-x-24 bg-pink-500 rounded-full opacity-30 group-hover:rotate-90 ease"></span>
                                            <span class="relative text-white">ADD TO CART</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <script>
        function removeElement(element) {
            element.remove();
        }
    </script>
</x-app-layout>
