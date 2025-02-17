
<div class="space-y-4">
    @foreach($announcements as $announcement)
    <a href="{{ route('announcements.show', $announcement) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="bg-secondary/10 p-2 rounded-lg">
                    <x-ri-newspaper-line class="size-5 fill-secondary" />
                </div>
                <span class="font-medium">{{ $announcement->title }}</span>
            </div>
            <div class="prose dark:prose-invert text-sm">
                {{ $announcement->published_at->diffForHumans() }}
            </div>
        </div>
        <p class="prose dark:prose-invert text-sm">{{ $announcement->description }}</p>
        </div>
    </a>
    @endforeach
</div>
