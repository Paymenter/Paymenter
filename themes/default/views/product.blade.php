<x-app-layout>
    <x-slot name="title">
        {{ __('Products') }}
    </x-slot>
    <div class="dark:bg-darkmode dark:text-darkmodetext py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
                    <!-- display all categories with products -->
                    <h1 class="text-center text-2xl font-bold">{{ __('Categories') }}</h1>
                    @if ($categories->count() < 1)
                        <div class="dark:bg-darkmode px-4 py-5 sm:px-6">
                            <h3 class="dark:text-darkmodetext text-lg leading-6 font-medium text-gray-900">
                                {{ __('Categories') }}
                            </h3>
                            <p class="dark:text-darkmodetext mt-1 max-w-2xl text-sm text-gray-500">
                                {{ __('Category not found!') }}
                            </p>
                        </div>
                    @endif
                    @foreach ($categories as $category)
                        <div class="mt-4">
                            <h2 class="text-xl font-bold text-left">{{ $category->name }}</h2>
                            <hr class="mb-4 mt-1 border-b-1 border-gray-300 dark:border-gray-600"></hr>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                                @foreach ($category->products as $product)
                                    <a href="{{ route('checkout.add') }}?id={{ $product->id }}" class="p-4 transition rounded-lg delay-400 border dark:border-darkmode hover:shadow-md flex flex-col bg-gray-100 dark:bg-darkmode">
                                        <img class="rounded-lg" src="{{ $product->image }}"
                                            alt="{{ $product->name }}"
                                            class="object-cover object-center mx-auto block h-64" onerror="removeElement(this);">
                                        <div class="mt-2">
                                            <h3 class="text-lg font-medium text-center text-gray-900 dark:text-darkmodetext">{{ $product->name }}</h3>
                                            <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600"></hr>
                                            <p class="mt-1 prose dark:prose-invert text-sm text-center text-gray-500 dark:text-darkmodetext">
                                                {{ \Illuminate\Mail\Markdown::parse($product->description) }}
                                            </p>
                                            <p class="mt-2 text-center text-gray-500 dark:text-darkmodetext">
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
</x-app-layout>


<script>
    function removeElement(element) {
        element.remove();
        this.error = true;
    }
</script>