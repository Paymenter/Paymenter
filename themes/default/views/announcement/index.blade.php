<x-app-layout title="{{ __('Announcements') }}">

    <div class="content">
        <h2 class="font-semibold text-2xl mb-2 text-secondary-900">{{ __('Announcements') }}</h2>
        @if ($announcements->count() > 0)
            <div class="grid grid-cols-12 gap-4">
                @foreach ($announcements->sortByDesc('created_at') as $announcement)
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="content-box">
                            <h3 class="font-semibold text-lg">{{ $announcement->title }}</h3>
                            <p>@markdownify(substr($announcement->announcement, 0, 100) . '...')</p>
                            <div class="flex justify-between items-center mt-3">
                                <span class="text-sm text-secondary-600">{{ __('Published') }}
                                    {{ $announcement->created_at->diffForHumans() }}</span>
                                <a href="{{ route('announcements.view', $announcement->id) }}"
                                    class="button button-secondary">{{ __('Read More') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <p class="text-center py-3">{{ __('Announcement not found!') }}</p>
        @endif
    </div>

</x-app-layout>
