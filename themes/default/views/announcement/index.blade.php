<x-app-layout>
    <x-slot name="title">
        {{ __('Announcements') }}
    </x-slot>
    <div class="dark:bg-darkmode dark:text-darkmodetext py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white rounded-lg shadow-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
                    <h1 class="text-center text-2xl font-bold">{{ __('Announcements') }}</h1>
                    @if ($announcements->count() < 1)
                        <div class="dark:bg-darkmode px-4 py-5 sm:px-6">
                            <p class="dark:text-darkmodetext mt-1 max-w-2xl text-sm text-gray-500">
                                {{ __('Announcement not found!') }}
                            </p>
                        </div>
                    @endif
                    <hr class="mb-4 mt-1 border-b-1 border-gray-300 dark:border-gray-600">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ($announcements as $announcement)
                            <a href="{{ route('announcements.view', $announcement->id) }}"
                                class="p-4 transition rounded-lg delay-400 border dark:border-darkmode hover:shadow-md dark:hover:shadow-gray-500 flex flex-col bg-gray-100 dark:bg-darkmode break-all">
                                <div class="mt-2 h-full relative">
                                    <h3 class="text-lg font-medium text-center text-gray-900 dark:text-darkmodetext">
                                        {{ $announcement->title }}</h3>
                                    <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                                    <div
                                        class="mt-1 prose dark:prose-invert text-gray-500 dark:text-darkmodetext break-words">
                                        {!! Str::Markdown(str_replace("\n", '<br>', substr($announcement->announcement, 0, 200). ' ...')) !!}
                                    </div>
                                    <br>
                                    <p class="mt-1 text-base text-center text-gray-500 dark:text-darkmodetext mx-auto w-full bottom-0 absolute font-black"
                                        data-tooltip-target="tooltip-{{ $announcement->id }}">
                                        {{ $announcement->created_at->diffForHumans() }}
                                    </p>
                                    <div id="tooltip-{{ $announcement->id }}" role="tooltip"
                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                        {{ $announcement->created_at }}
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
