<div class="container mt-14">
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-semibold">{{ __('knowledgebase::messages.title') }}</h1>
            <p class="mt-2 text-base">{{ __('knowledgebase::messages.subtitle') }}</p>
        </div>

        <div class="flex flex-col gap-4">
            <div class="relative">
                <input type="search" wire:model.debounce.300ms="search"
                    class="w-full rounded-lg border border-neutral bg-background-secondary px-4 py-3 pr-12 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                    placeholder="{{ __('knowledgebase::messages.search_placeholder') }}" />
                @if ($searchTerm !== '')
                    <button type="button" wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-3 flex items-center text-base/60 hover:text-primary">
                        <x-ri-close-circle-line class="size-5" />
                    </button>
                @endif
            </div>

            @if ($searchTerm !== '')
                <div class="rounded-lg border border-neutral bg-background-secondary p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">{{ __('knowledgebase::messages.search_results_heading') }}
                        </h2>
                        <span
                            class="text-xs text-base/60">{{ trans_choice('knowledgebase::messages.results_count', $searchResults->count(), ['count' => $searchResults->count()]) }}</span>
                    </div>

                    @if ($searchResults->isEmpty())
                        <p class="mt-3 text-sm text-base/70">{{ __('knowledgebase::messages.no_results') }}</p>
                    @else
                        <ul class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            @foreach ($searchResults as $result)
                                <li>
                                    <div class="rounded-lg border border-neutral bg-background-secondary/80 p-4">
                                        <div class="flex items-center gap-2 text-xs text-base/60">
                                            <x-ri-hashtag class="size-4" />
                                            <a href="{{ route('knowledgebase.category', $result->category) }}"
                                                wire:navigate class="font-medium text-primary-600 hover:underline">
                                                {{ $result->category->name }}
                                            </a>
                                        </div>

                                        <div class="mt-3 flex flex-col gap-3">
                                            <a href="{{ route('knowledgebase.show', $result) }}" wire:navigate
                                                class="text-lg font-semibold text-base hover:text-primary-600">
                                                {{ $result->title }}
                                            </a>
                                            @php
                                                $searchExcerpt = \Illuminate\Support\Str::of(
                                                    $result->summary ?: strip_tags($result->content),
                                                )
                                                    ->squish()
                                                    ->limit(200);
                                            @endphp
                                            @if ($searchExcerpt->isNotEmpty())
                                                <p class="text-sm text-base/70">{{ $searchExcerpt }}</p>
                                            @endif
                                            <div>
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

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($categories as $category)
                <div
                    class="rounded-lg border border-neutral bg-background-secondary hover:bg-background-secondary/80 p-4 transition flex flex-col gap-4">
                    @php
                        $categoryDescription = \Illuminate\Support\Str::of($category->description ?? '')
                            ->stripTags()
                            ->squish();
                    @endphp

                    <div class="flex flex-1 items-start justify-between gap-4">
                        <div class="flex flex-col gap-3">
                            <h2 class="text-xl font-semibold">{{ $category->name }}</h2>
                            @if ($categoryDescription->isNotEmpty())
                                <p class="text-sm text-base/70">
                                    {{ $categoryDescription->limit(180) }}
                                </p>
                            @endif
                        </div>
                        <span class="rounded-full bg-primary-600/10 px-3 py-1 text-xs font-medium text-primary-600">
                            {{ trans_choice('knowledgebase::messages.articles_count', $category->articles_count, ['count' => $category->articles_count]) }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('knowledgebase.category', $category) }}" wire:navigate class="block">
                            <x-button.primary class="w-full">
                                {{ __('knowledgebase::messages.view_articles') }}
                            </x-button.primary>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
