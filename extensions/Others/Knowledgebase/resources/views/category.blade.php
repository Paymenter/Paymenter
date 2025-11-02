<div class="container mt-14">
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-semibold">{{ $category->name }}</h1>
            @php
                $categoryDescription = \Illuminate\Support\Str::of($category->description ?? '')
                    ->stripTags()
                    ->squish();
            @endphp
            @if ($categoryDescription->isNotEmpty())
                <p class="mt-2 text-base">{{ $categoryDescription }}</p>
            @endif
        </div>

        <div class="flex flex-col gap-4">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full rounded-lg border border-neutral bg-background-secondary px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-0"
                    placeholder="{{ __('knowledgebase::messages.search_placeholder') }}" />
            </div>

            @if ($searchTerm !== '' && $searchResults)
                <div class="mt-3 rounded-lg border border-neutral bg-background-secondary p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-lg font-semibold">{{ __('knowledgebase::messages.search_results_heading') }}</h2>
                        <span
                            class="text-xs text-base/60">{{ trans_choice('knowledgebase::messages.results_count', $searchResults->total(), ['count' => $searchResults->total()]) }}</span>
                    </div>

                    @if ($searchResults->isEmpty())
                        <p class="mt-3 text-sm text-base/70">{{ __('knowledgebase::messages.no_results') }}</p>
                    @else
                        <ul class="mt-4 flex flex-col gap-3">
                            @foreach ($searchResults as $result)
                                <li>
                                    <div
                                        class="rounded-lg border border-neutral bg-background-secondary hover:bg-background-secondary/80 p-4 transition">
                                        <div class="flex flex-col gap-3">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex flex-col gap-1">
                                                    <a href="{{ route('knowledgebase.category', $result->category) }}"
                                                        wire:navigate class="text-xs uppercase text-base/60 hover:text-primary">
                                                        {{ $result->category->name }}
                                                    </a>
                                                    <a href="{{ route('knowledgebase.show', $result) }}" wire:navigate
                                                        class="text-lg font-semibold text-primary-600">
                                                        {{ $result->title }}
                                                    </a>
                                                </div>
                                                @if ($result->published_at)
                                                    <span class="text-xs text-base/60">
                                                        {{ $result->published_at->timezone(config('app.timezone'))->translatedFormat('M d, Y') }}
                                                    </span>
                                                @endif
                                            </div>

                                            @php
                                                $searchExcerpt = \Illuminate\Support\Str::of(
                                                    $result->summary ?: strip_tags($result->content),
                                                )
                                                    ->squish()
                                                    ->limit(220);
                                            @endphp
                                            @if ($searchExcerpt->isNotEmpty())
                                                <p class="text-sm text-base/70">{{ $searchExcerpt }}</p>
                                            @endif

                                            <div class="flex items-center justify-between text-xs text-base/60">
                                                <div class="flex items-center gap-2">
                                                    <x-ri-book-read-line class="size-4" />
                                                    <span>{{ trans_choice('knowledgebase::messages.view_count', $result->view_count, ['count' => $result->view_count]) }}</span>
                                                </div>
                                                <a href="{{ route('knowledgebase.show', $result) }}" wire:navigate>
                                                    <x-button.primary size="xs">
                                                        {{ __('knowledgebase::messages.view_article') }}
                                                    </x-button.primary>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </div>

        @if ($searchTerm === '')
            @if ($children->isNotEmpty())
                <div class="flex flex-col gap-4">
                    <span class="text-xs font-semibold uppercase text-base/50">{{ __('knowledgebase::messages.subcategories') }}</span>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($children as $child)
                            <div
                                class="rounded-lg border border-neutral bg-background-secondary hover:bg-background-secondary/80 p-4 transition flex flex-col gap-4">
                                @php
                                    $childDescription = \Illuminate\Support\Str::of($child->description ?? '')
                                        ->stripTags()
                                        ->squish();
                                    $childArticlesCount = $child->publishedArticles->count();
                                @endphp

                                <div class="flex flex-1 items-start justify-between gap-4">
                                    <div class="flex flex-col gap-3">
                                        <h2 class="text-xl font-semibold">{{ $child->name }}</h2>
                                        @if ($childDescription->isNotEmpty())
                                            <p class="text-sm text-base/70">
                                                {{ $childDescription->limit(180) }}
                                            </p>
                                        @endif
                                    </div>
                                    <span class="rounded-full bg-primary-600/10 px-3 py-1 text-xs font-medium text-primary-600">
                                        {{ trans_choice('knowledgebase::messages.articles_count', $childArticlesCount, ['count' => $childArticlesCount]) }}
                                    </span>
                                </div>

                                <div>
                                    <a href="{{ route('knowledgebase.category', $child) }}" wire:navigate class="block">
                                        <x-button.primary class="w-full">
                                            {{ __('knowledgebase::messages.view_articles') }}
                                        </x-button.primary>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                @foreach ($articles as $article)
                    <div
                        class="rounded-lg border border-neutral bg-background-secondary hover:bg-background-secondary/80 p-4 transition">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-4">
                                <h2 class="text-lg font-semibold text-primary-600">{{ $article->title }}</h2>
                                @if ($article->published_at)
                                    <span class="text-xs text-base/60">
                                        {{ $article->published_at->timezone(config('app.timezone'))->translatedFormat('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                            @php
                                $preview = \Illuminate\Support\Str::of($article->summary ?: strip_tags($article->content))
                                    ->squish()
                                    ->limit(220);
                            @endphp
                            @if ($preview->isNotEmpty())
                                <p class="text-sm text-base/70">{{ $preview }}</p>
                            @endif
                            <div class="flex items-center justify-between text-xs text-base/60">
                                <div class="flex items-center gap-2">
                                    <x-ri-book-read-line class="size-4" />
                                    <span>{{ trans_choice('knowledgebase::messages.view_count', $article->view_count, ['count' => $article->view_count]) }}</span>
                                </div>
                                <a href="{{ route('knowledgebase.show', $article) }}" wire:navigate>
                                    <x-button.primary size="xs">
                                        {{ __('knowledgebase::messages.view_article') }}
                                    </x-button.primary>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

