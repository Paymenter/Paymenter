<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
    <x-success class="mt-4" />
    <div class="container w-11/12 h-full px-6 py-10 mx-auto md:w-4/5">
        <div class="w-full h-full rounded">
            <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-darkmode2">
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
                        <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-darkmode2">
                            <div class="p-6 bg-white dark:bg-darkmode2">
                                <!-- display all categories with products -->
                                <h1 class="text-2xl font-bold text-center">{{ __('Categories') }}</h1>
                                @foreach ($categories as $category)
                                    <div class="mt-4">
                                        <h2 class="text-xl font-bold text-left">{{ $category->name }}</h2>
                                        <hr class="mb-4 mt-1 border-b-1 border-gray-300 dark:border-gray-600">
                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                                            @foreach ($category->products as $product)
                                                <a href="{{ route('checkout.add') }}?id={{ $product->id }}"
                                                    class="p-4 transition rounded-lg delay-400 border dark:border-darkmode hover:shadow-md flex flex-col bg-gray-100 dark:bg-darkmode">
                                                    <img class="rounded-lg" src="{{ $product->image }}"
                                                        alt="{{ $product->name }}" onerror="removeElement(this);">
                                                    <div class="mt-2 h-full flex flex-col relative">
                                                        <h3
                                                            class="text-lg font-medium text-center text-gray-900 dark:text-darkmodetext">
                                                            {{ $product->name }}</h3>
                                                        <hr
                                                            class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                                                        <p
                                                            class="mt-1 prose dark:prose-invert text-sm text-center text-gray-500 dark:text-darkmodetext">
                                                            {{ \Illuminate\Mail\Markdown::parse($product->description) }}
                                                        </p>
                                                        <br>
                                                        <p
                                                            class="mt-1 text-md text-center text-gray-500 dark:text-darkmodetext mx-auto  w-full bottom-0 absolute">
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

<script>
    function removeElement(element) {
        element.remove();
    }
</script>
