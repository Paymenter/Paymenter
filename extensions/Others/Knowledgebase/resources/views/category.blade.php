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
    </div>
</div>
