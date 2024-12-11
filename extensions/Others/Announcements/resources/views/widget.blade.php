
<div class="space-y-4">
    @foreach($announcements as $announcement)
    <a href="{{ route('announcements.show', $announcement) }}" wire:navigate>
        <div class="bg-[#1e293b] hover:bg-background-secondary/80 p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="bg-[#2d3b4f] p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 20V4H4V19C4 19.5523 4.44772 20 5 20H16ZM19 22H5C3.34315 22 2 20.6569 2 19V3C2 2.44772 2.44772 2 3 2H17C17.5523 2 18 2.44772 18 3V10H22V19C22 20.6569 20.6569 22 19 22ZM18 12V19C18 19.5523 18.4477 20 19 20C19.5523 20 20 19.5523 20 19V12H18ZM6 6H12V12H6V6ZM8 8V10H10V8H8ZM6 13H14V15H6V13ZM6 16H14V18H6V16Z"></path>
                    </svg>
                </div>
                <span class="text-white font-medium">{{ $announcement->title }}</span>
            </div>
            <div class="text-gray-400 text-sm">
                {{ $announcement->published_at->diffForHumans() }}
            </div>
        </div>
        <p class="text-gray-400 text-sm">{{ $announcement->description }}</p>
        </div>
    </a>
    @endforeach
</div>
