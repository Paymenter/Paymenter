<x-app-layout>
    <x-slot name="title">
        {{ __('Home') }}
    </x-slot>
    <script>
        function removeElement(element) {
            element.remove();
        }
    </script>
    <x-success class="mt-4" />
    <div class="container w-11/12 h-full px-6 py-10 mx-auto md:w-4/5">
        <div class="w-full h-full rounded">
            <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-darkmode2">
                    <div class="p-6 bg-white dark:bg-darkmode2 sm:px-20">
                        <div class="prose dark:prose-invert max-w-full	">
                            @if (config('settings::home_page_text'))
                                {{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', config('settings::home_page_text'))) }}
                            @endif
                        </div>
                    </div>
                </div>
                @if ($announcements->count() > 0)
                    <br>
                    <div class="overflow-hidden bg-white dark:bg-darkmode2 rounded-lg shadow-lg">
                        <div class="p-6 bg-white dark:bg-darkmode2 sm:px-20">
                            <h1 class="text-2xl font-bold text-center">{{ __('Announcements') }}</h1>
                            <div class="mt-4">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                                    @foreach ($announcements as $announcement)
                                        <a href="{{ route('announcements.view', $announcement->id) }}"
                                            class="p-4 transition rounded-lg delay-400 border dark:border-darkmode hover:shadow-md dark:hover:shadow-gray-500 flex flex-col bg-gray-100 dark:bg-darkmode break-all mt-2 h-full relative">
                                            <div class="mt-2 h-full relative">
                                                <h2
                                                    class="text-lg font-medium text-center text-gray-900 dark:text-darkmodetext break-normal">
                                                    {{ $announcement->title }}</h2>
                                                <hr>
                                                <div class="mt-1 text-gray-500 dark:text-darkmodetext">
                                                    {{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', substr($announcement->announcement, 0, 100). ' ...')) }}
                                                </div>
                                                <br>
                                                <p
                                                    class="mt-1 text-base text-center text-gray-500 dark:text-darkmodetext mx-auto w-full bottom-0 absolute font-black"  data-tooltip-target="tooltip-{{ $announcement->id }}">
                                                    {{ $announcement->created_at->diffForHumans() }}
                                                </p>
                                                <div id="tooltip-{{ $announcement->id }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                    {{ $announcement->created_at }}
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="py-12 dark:bg-darkmode dark:text-darkmodetext">
                    <div class="px-0 mx-auto max-w-7xl">
                        <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-darkmode2">
                            <div class="p-6 bg-white dark:bg-darkmode2">
                                <!-- display all categories with products -->
                                <h1 class="text-2xl font-bold text-center">{{ __('Categories') }}</h1>
                                @foreach ($categories as $category)
                                    @if($category->products->count() == 0)
                                        @continue
                                    @endif
                                    <div class="mt-4">
                                        <h2 class="text-xl font-bold text-left">{{ $category->name }}</h2>
                                        <hr class="mb-4 mt-1 border-b-1 border-gray-300 dark:border-gray-600">
                                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                                            @foreach ($category->products as $product)
                                                <a href="{{ route('checkout.add', $product->id) }}"
                                                    class="p-4 transition rounded-lg delay-400 border dark:border-darkmode hover:shadow-md dark:hover:shadow-gray-500 flex flex-col bg-gray-100 dark:bg-darkmode break-all">
                                                    <img class="rounded-lg" src="{{ $product->image }}"
                                                        alt="{{ $product->name }}" onerror="removeElement(this);">
                                                    <div class="mt-2 h-full relative">
                                                        <h3
                                                            class="text-lg font-medium text-center text-gray-900 dark:text-darkmodetext break-normal">
                                                            {{ $product->name }}</h3>
                                                        <hr
                                                            class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                                                        <div
                                                            class="mt-1 prose dark:prose-invert text-gray-500 dark:text-darkmodetext">
                                                            {{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', $product->description)) }}
                                                        </div>
                                                        <br><br>
                                                        <p
                                                            class="mt-1 text-base text-center text-gray-500 dark:text-darkmodetext mx-auto w-full bottom-0 absolute font-black">
                                                            {{ $product->price() ? config('settings::currency_sign') . $product->price() : __('Free') }}
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
</x-app-layout>