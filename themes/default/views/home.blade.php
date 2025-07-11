<div>
    <div class="flex flex-col gap-6">
        <div class="mx-auto container bg-background-secondary p-4 rounded-md">
            <article class="prose dark:prose-invert max-w-full">
                {!! Str::markdown(theme('home_page_text', 'Welcome to Paymenter'), [
                'allow_unsafe_links' => false,
                'renderer' => [
                    'soft_break' => "<br>"
                ]]) !!}
            </article>
        </div>
        <div class="mx-auto container rounded-md grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            @foreach ($categories as $category)
                <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
                    @if(theme('small_images', false))
                        <div class="flex gap-x-3 items-center">
                    @endif
                    @if ($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                            class="rounded-md {{ theme('small_images', false) ? 'w-14 h-fit' : 'w-full object-cover object-center' }}">
                    @endif
                    <h2 class="text-xl font-bold">{{ $category->name }}</h2>
                    @if(theme('small_images', false))
                        </div>
                    @endif
                    @if(theme('show_category_description', true))
                        <article class="mt-2 prose dark:prose-invert">
                            {!! $category->description !!}
                        </article>
                    @endif
                    <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="mt-2">
                        <x-button.primary>
                            {{ __('general.view') }}
                        </x-button.primary>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    {!! hook('pages.home') !!}
</div>
