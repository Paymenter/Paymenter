<div class="mx-auto container mt-4">
    <div class="bg-background-secondary p-4 rounded-md">
        <div class="flex flex-row justify-between mb-6">
            <h2 class="text-xl font-bold">{{ $announcement->title }}</h2>
            <p class="text-sm text-gray-400">{{ $announcement->published_at->diffForHumans() }}</p>
        </div>
        <article class="prose prose-invert mb-2 max-w-full">
            {!! $announcement->content !!}
        </article>
    </div>
</div>