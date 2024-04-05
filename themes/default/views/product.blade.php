<x-app-layout description="{{ $category->description ?? null }}" title="{{ $category->name ?? __('Products') }}">
    <x-success />
    <script>
        function removeElement(element) {
            element.remove();
            this.error = true;
        }
    </script>
    <div class="content">
        <div class="grid grid-cols-12 gap-4">
            @if ($categories->count() > 0)
                <div class="lg:col-span-3 col-span-12">
                    <div class="content-box">
                        <div class="flex gap-x-2 items-center">
                            <div
                                class="bg-primary-400 w-8 h-8 flex items-center justify-center rounded-md text-gray-50 text-xl">
                                <i class="ri-list-indefinite"></i>
                            </div>
                            <h3 class="font-semibold text-lg">{{ __('Categories') }}</h3>
                        </div>
                        <div class="flex flex-col gap-2 mt-2">
                            @foreach ($categories as $categoryItem)
                                @php $hasActiveChild = false; @endphp
                                @if ($categoryItem->children->count() > 0)
                                    @foreach ($categoryItem->children as $childCategory)
                                        @if ($category->name == $childCategory->name)
                                            @php $hasActiveChild = true; @endphp
                                        @endif
                                    @endforeach
                                @endif
                                <div class="flex flex-col gap-1 group duration-150">
                                    @if ($categoryItem->products()->where('hidden', false)->count() > 0 || $categoryItem->children->count() > 0)
                                        <a href="{{ route('products', $categoryItem->slug) }}"
                                            class="@if ($category->name == $categoryItem->name || $hasActiveChild) text-secondary-900 pl-3 !border-primary-400 @endif border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                            {{ $categoryItem->name }}
                                        </a>
                                    @endif
                                    @if($categoryItem->children->count() > 0)
                                        <div class="flex flex-col gap-1 @if(!$hasActiveChild) hidden @endif group-hover:flex transition ease-in-out duration-300">
                                            @foreach ($categoryItem->children as $childCategory)
                                                <a href="{{ route('products', $childCategory->slug) }}"
                                                    class="pl-6 text-sm @if ($category->name == $childCategory->name) text-secondary-900 pl-3 !border-primary-400 @endif border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-8 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                                    {{ $childCategory->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="@if ($categories->count() > 0) lg:col-span-9 @endif col-span-12">
                <div class="content-box">
                    <div class="flex flex-row">
                        @if($category->image)
                            <img src="/storage/categories/{{ $category->image }}" class="w-20 h-full rounded-md mr-4" />
                        @endif
                        <div class="w-full">
                            <h1 class="text-3xl font-semibold text-secondary-900">{{ $category->name }}</h1>
                            <div class="prose dark:prose-invert">
                                @markdownify($category->description)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-4 mt-4">
                    @foreach($category->children as $childCat)
                        <div class="lg:col-span-4 md:col-span-6 col-span-12">
                            <div class="content-box h-full flex flex-col">
                                <div class="flex items-center gap-x-3 mb-2">
                                    @if($childCat->image)
                                        <img src="/storage/categories/{{ $childCat->image }}" class="w-14 rounded-md" onerror="removeElement(this);" />
                                    @endif
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $childCat->name }}</h3>
                                    </div>
                                </div>
                                <div class="prose dark:prose-invert">@markdownify($childCat->description)</div>
                                <div class="pt-3 mt-auto">
                                    <a href="{{ route('products', $childCat->slug) }}"
                                    class="button button-secondary w-full">{{ __('Browse Category') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    @foreach ($category->products()->where('hidden', false)->with('prices')->orderBy('order')->get() as $product)
                        <livewire:product :product="$product" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
