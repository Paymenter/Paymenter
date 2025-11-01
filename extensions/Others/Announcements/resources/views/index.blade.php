<div class="container mt-14">
    @if(count($announcements) > 0)
    <div class="flex flex-col gap-5">

        <h2 class="text-xl font-semibold">{{ __('Announcements') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($announcements as $announcement)
            <div class="flex flex-col bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg">
                <h2 class="text-xl font-bold">{{ $announcement->title }}</h2>
                <div class="my-2 text-sm text-base/70">
                    {{ $announcement->description }}
                </div>
                <a href="{{ route('announcements.show', $announcement) }}" wire:navigate class="mt-auto pt-2">
                    <x-button.primary>
                        {{ __('common.button.view') }}
                    </x-button.primary>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>