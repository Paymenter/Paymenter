<x-app-layout>
    <x-slot name="title">
        {{ $announcement->title }}
    </x-slot>
    <!-- View Announcement -->
    <div class="dark:bg-darkmode dark:text-darkmodetext py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
                    <h1 class="text-2xl font-bold">{{ $announcement->title }}</h1>
                    <p class="mt-1 text-base text-gray-500 dark:text-darkmodetext/80 items-center inline-flex">
                        <i class="ri-calendar-line"></i>{{ $announcement->created_at->format('d/m/Y') }}
                        <i class="ri-time-line ml-1"></i>{{ $announcement->created_at->format('H:i') }}
                    </p>
                    <hr class="mb-4 mt-1 border-b-1 border-gray-300 dark:border-gray-600">
                    <div class="prose dark:prose-invert text-gray-500 dark:text-darkmodetext max-w-full">
                        {!! Str::markdown(str_replace("\n", '<br>', $announcement->announcement)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
