<x-app-layout>
    <x-slot name="title">
        {{ $announcement->title }}
    </x-slot>
    
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
            <div>
                {!! Str::markdown(str_replace("\n", '<br>', $announcement->announcement)) !!}
            </div>
        </div>
    </div>

</x-app-layout>
