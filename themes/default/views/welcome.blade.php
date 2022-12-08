<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
    <x-success class="mt-4" />
    <div class="container w-11/12 h-full px-6 py-10 mx-auto md:w-4/5">
        <div class="w-full h-full rounded">
            <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white rounded-lg shadow-xl dark:bg-darkmode2">
                    <div class="p-6 bg-white dark:bg-darkmode2 sm:px-20">
                        <div class="prose dark:prose-invert">
                            @if (config('settings::home_page_text'))
                                {{ \Illuminate\Mail\Markdown::parse(config('settings::home_page_text')) }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="py-12 dark:bg-darkmode dark:text-darkmodetext">
                    <div class="px-0 mx-auto max-w-7xl">
                        <div class="overflow-hidden bg-white rounded-lg dark:bg-darkmode2">
                            <div class="p-6 bg-white dark:bg-darkmode2">
                                <!-- display all categories with products -->
                                <h1 class="text-2xl font-bold text-center">{{ __('Categories') }}</h1>
                                @foreach ($categories as $category)
                                    <div class="mt-4">
                                        <h2 class="text-xl font-bold text-center">{{ $category->name }}</h2>
                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                                            @foreach ($category->products as $product)
                                                <a href="{{ route('checkout.add') }}?id={{ $product->id }}"
                                                    class="p-4 transition rounded-lg delay-400 hover:shadow-lg dark:bg-darkmode flex flex-col">
                                                    <img class="rounded-lg object-cover mx-auto w-fit"
                                                        src="{{ $product->image }}" alt="{{ $product->name }}"
                                                        onerror="removeElement(this);">
                                                    <script>
                                                        function removeElement(element) {
                                                            element.remove();
                                                        }
                                                    </script>
                                                    <div class="mt-2 h-full flex flex-col relative">
                                                        <h3
                                                            class="text-lg font-medium text-center text-gray-900 dark:text-darkmodetext">
                                                            {{ $product->name }}</h3>
                                                        <p
                                                            class="mt-1 text-sm text-center text-gray-500 dark:text-darkmodetext">
                                                        <div class="prose dark:prose-invert">
                                                            {{ \Illuminate\Mail\Markdown::parse($product->description) }}
                                                        </div>
                                                        </p>
                                                        <br>
                                                        <p
                                                            class="mt-1 text-md text-center text-gray-500 dark:text-darkmodetext mx-auto  w-full bottom-0 absolute border-t border-logo">
                                                            @if ($product->price == 0)
                                                                {{ __('Free') }}
                                                            @else
                                                                {{ config('settings::currency_sign') }}{{ $product->price }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
