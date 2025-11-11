<div>
    <div class="flex flex-col gap-12">
        <!-- Hero Section -->
        <div class="mx-auto container text-center">
            <div class="kila-card bg-background-secondary border border-neutral p-8 md:p-12">
                <article class="prose dark:prose-invert max-w-full mx-auto">
                    {!! Str::markdown(theme('home_page_text', 'Welcome to Paymenter'), [
                    'allow_unsafe_links' => false,
                    'renderer' => [
                        'soft_break' => "<br>"
                    ]]) !!}
                </article>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="mx-auto container">
            <div class="flex items-center justify-center mb-8">
                <span class="kila-badge bg-primary text-white">{{ __('SERVICES') }}</span>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
                @foreach ($categories as $category)
                    <div class="kila-card flex flex-col bg-background-secondary border border-neutral p-6">
                        @if(theme('small_images', false))
                            <div class="flex gap-x-4 items-center mb-4">
                        @endif
                        @if ($category->image)
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                                class="rounded-md {{ theme('small_images', false) ? 'w-16 h-16 object-cover' : 'w-full h-48 object-cover object-center mb-4' }}">
                        @else
                            <div class="w-full h-48 bg-primary/10 rounded-md mb-4 flex items-center justify-center">
                                <svg class="w-16 h-16 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-grow">
                            <h2 class="text-2xl font-bold mb-3">{{ $category->name }}</h2>
                            @if(theme('small_images', false))
                                </div>
                            @endif
                            @if(theme('show_category_description', true))
                                <article class="mb-4 prose dark:prose-invert text-sm text-muted line-clamp-3">
                                    {!! $category->description !!}
                                </article>
                            @endif
                            <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate class="block mt-auto">
                                <x-button.primary class="w-full btn-kila-primary">
                                    {{ __('Browse Products') }}
                                    <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </x-button.primary>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {!! hook('pages.home') !!}
</div>
