<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
    <x-success class="mt-4" />   
        <div class="container mx-auto py-10 h-64 md:w-4/5 h-full w-11/12 px-6">
            <div class="w-full h-full rounded">
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <div class="dark:bg-darkmode2 bg-white overflow-hidden shadow-xl rounded-lg">
                        <div class="dark:bg-darkmode2 p-6 sm:px-20 bg-white">
                            <div class="prose dark:prose-invert">
                                {{ \Illuminate\Mail\Markdown::parse(\App\Models\Settings::first()->home_page_text) }}
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
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                                @foreach ($category->products as $product)
                                                    <div
                                                        class="transition delay-400 hover:shadow-lg dark:bg-darkmode rounded-lg p-4">
                                                        <a href="{{ route('checkout.add') }}?id={{ $product->id }}">
                                                            <img class="rounded-lg" src="{{ $product->image }}"
                                                                alt="{{ $product->name }}"
                                                                class="w-full h-64 object-cover object-center">
                                                            <div class="mt-2">
                                                                <h3
                                                                    class="text-center dark:text-darkmodetext text-lg font-medium text-gray-900">
                                                                    {{ $product->name }}</h3>
                                                                <p
                                                                    class="text-center dark:text-darkmodetext mt-1 text-sm text-gray-500">
                                                                <div class="prose dark:prose-invert">
                                                                    {{ \Illuminate\Mail\Markdown::parse($product->description) }}
                                                                </div>
                                                                </p>
                                                                <p
                                                                    class="text-center dark:text-darkmodetext mt-1 text-sm text-gray-500">
                                                                    @if( App\Models\Settings::first()->currency_position == '1' )
                                                                        {{ App\Models\Settings::first()->currency_sign }} {{ number_format($product->price, 2) }}
                                                                    @elseif( App\Models\Settings::first()->currency_position == '0' )
                                                                        {{ number_format($product->price, 2) }} {{ App\Models\Settings::first()->currency_sign }}
                                                                    @endif
                                                                </p>
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
