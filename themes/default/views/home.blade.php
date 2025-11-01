<div>
    <div class="flex flex-col gap-6">
        <div class="w-full bg-background-secondary p-14 rounded-md border-b border-neutral">
            <div class="container">
                <article class="prose dark:prose-invert max-w-full ">
                    {!! Str::markdown(theme('home_page_text', 'Welcome to Paymenter'), [
                    'allow_unsafe_links' => false,
                    'renderer' => [
                    'soft_break' => "<br>"
                    ]]) !!}
                </article>
            </div>
        </div>

        <div class="container mt-4 flex flex-col gap-5">

            <h2 class="text-xl font-semibold">Services</h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 mb-4">
                @foreach ($categories as $category)
                <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
                    @if(theme('small_images', false))
                    <div class="flex gap-x-3 items-center">
                        @endif
                        @if ($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                            class="aspect-square rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
                        @endif
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold">{{ $category->name }}</h3>
                        </div>
                        @if(theme('small_images', false))
                    </div>
                    @endif
                    @if(theme('show_category_description', true))
                    <article class="prose dark:prose-invert mt-2">
                        {!! $category->description !!}
                    </article>
                    @endif
                    <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="mt-auto pt-2">
                        <x-button.primary>
                            {{ __('common.button.view_all') }}
                            <x-ri-arrow-right-fill class="size-5" />
                        </x-button.primary>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    {!! hook('pages.home') !!}
</div>
