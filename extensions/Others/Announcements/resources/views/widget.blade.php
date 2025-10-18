@if($announcements->count() > 0) 
<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-background-secondary border border-neutral p-2 rounded-lg">
                <x-ri-megaphone-fill class="size-5" />
            </div>
            <h2 class="text-xl font-semibold">{{ __('Announcements') }}</h2>
        </div>
    </div>
    <div class="space-y-4">
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
                    <p class="text-sm text-base/70">{{ $announcement->description }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    <x-navigation.link class="bg-background-secondary hover:bg-background-secondary/80 bg-background-secondary hover:bg-background-secondary/80 border border-neutral flex items-center justify-center rounded-lg flex items-center justify-center rounded-lg"
        :href="route('announcements.index')">
        {{ __('dashboard.view_all') }}
        <x-ri-arrow-right-fill class="size-5" />
    </x-navigation.link>
</div>
@endif