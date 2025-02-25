<div>
    <div class="flex flex-col gap-6">
        <div class="mx-auto container bg-background-secondary p-4 rounded-md">
            <article class="prose dark:prose-invert max-w-full">
                {!! Str::markdown(theme('home_page_text', 'Welcome to Paymenter'), [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                ]) !!}
            </article>
        </div>
        <div class="mx-auto container rounded-md grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            @foreach ($categories as $category)
                <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
                    @if ($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                            class="w-full object-cover object-center rounded-md">
                    @endif
                    <h2 class="text-xl font-bold mb-2">{{ $category->name }}</h2>
                    <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate>
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
