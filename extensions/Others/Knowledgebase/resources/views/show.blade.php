<div class="container mt-14">
    <div class="flex flex-col gap-6">
        <div
            class="rounded-lg border border-neutral bg-background-secondary hover:bg-background-secondary/80 p-4 transition">
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between gap-4">
                    <div class="space-y-2">
                        <span
                            class="text-xs uppercase tracking-wide text-primary-600">{{ $article->category->name }}</span>
                        <h1 class="text-2xl font-semibold text-base">{{ $article->title }}</h1>
                        @if ($article->summary)
                            <p class="text-sm text-base/70">{{ $article->summary }}</p>
                        @endif
                    </div>
                    <div class="text-sm text-base/60 flex flex-col items-end gap-1">
                        @if ($article->published_at)
                            <span>{{ $article->published_at->timezone(config('app.timezone'))->translatedFormat('M d, Y') }}</span>
                        @endif
                        <span>{{ trans_choice('knowledgebase::messages.view_count', $article->view_count, ['count' => $article->view_count]) }}</span>
                    </div>
                </div>

                <div class="mt-3 h-px w-full bg-neutral-200 dark:bg-neutral-700"></div>

                <article class="prose dark:prose-invert max-w-full">
                    {!! $article->content !!}
                </article>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                @if ($previousArticle)
                    <a href="{{ route('knowledgebase.show', $previousArticle) }}" wire:navigate class="block">
                        <x-button.secondary class="w-full justify-center gap-2">
                            <x-ri-arrow-left-s-line class="size-4" />
                            <span>{{ __('knowledgebase::messages.previous_article') }}</span>
                        </x-button.secondary>
                    </a>
                @else
                    <x-button.secondary class="w-full justify-center gap-2" disabled>
                        <x-ri-arrow-left-s-line class="size-4" />
                        <span>{{ __('knowledgebase::messages.previous_article') }}</span>
                    </x-button.secondary>
                @endif
            </div>

            <div>
                @if ($nextArticle)
                    <a href="{{ route('knowledgebase.show', $nextArticle) }}" wire:navigate class="block">
                        <x-button.primary class="w-full justify-center gap-2">
                            <span>{{ __('knowledgebase::messages.next_article') }}</span>
                            <x-ri-arrow-right-s-line class="size-4" />
                        </x-button.primary>
                    </a>
                @else
                    <x-button.primary class="w-full justify-center gap-2" disabled>
                        <span>{{ __('knowledgebase::messages.next_article') }}</span>
                        <x-ri-arrow-right-s-line class="size-4" />
                    </x-button.primary>
                @endif
            </div>
        </div>
    </div>
</div>
