<x-app-layout title="{{ $announcement->title }}" description='{{ strip_tags(Str::markdown(nl2br(Stevebauman\Purify\Facades\Purify::clean($announcement->announcement)))) }}'>
    
    <!-- View Announcement -->
    <div class="content">
        <div class="content-box max-w-6xl mx-auto">
            <div class="flex justify-between">
                <h1 class="text-2xl font-bold">{{ $announcement->title }}</h1>
                <div class="flex items-center gap-x-5">
                    <p class="text-secondary-600 flex items-center gap-x-2">
                        <i class="ri-calendar-line"></i>
                        {{ $announcement->created_at->format('d/m/Y') }}
                    </p>
                    <p class="text-secondary-600 flex items-center gap-x-2">
                        <i class="ri-time-line ml-1"></i>
                        {{ $announcement->created_at->format('H:i') }}
                    </p>
                </div>
            </div>
            <div class="prose dark:prose-invert max-w-full">
                @markdownify($announcement->announcement)
            </div>
        </div>
    </div>

</x-app-layout>
