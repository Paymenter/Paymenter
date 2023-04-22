<x-app-layout title="home">
    <x-success class="mt-4" />

    @if (config('settings::home_page_text'))
        <div class="content ">
            <div class="content-box">
                <div class="prose dark:prose-invert min-w-full">
                    {{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', config('settings::home_page_text'))) }}
                </div>
            </div>
        </div>
    @endif

    @if ($categories->count() > 0)
        <div class="content">
            <h2 class="font-semibold text-2xl mb-2 text-secondary-900">{{ __('Categories') }}</h2>
            <div class="grid grid-cols-12 gap-4">

                @foreach ($categories as $category)
                    @if ($category->products->count() > 0)
                        <div class="lg:col-span-3 md:col-span-6 col-span-12">
                            <div class="content-box">
                                <h3 class="font-semibold text-lg">{{ $category->name }}</h3>
                                <p>{{ $category->description }}</p>
                                <a href="{{ route('products', $category->slug) }}"
                                    class="button button-secondary w-full mt-3">{{ __('Browse Category') }}</a>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        </div>
    @endif

    @if ($announcements->count() > 0)
        <div class="content">
            <h2 class="font-semibold text-2xl mb-2 text-secondary-900">{{ __('Announcements') }}</h2>
            <div class="grid grid-cols-12 gap-4">
                @foreach ($announcements as $announcement)
                    <div class="lg:col-span-4 md:col-span-6 col-span-12">
                        <div class="content-box">
                            <h3 class="font-semibold text-lg">{{ $announcement->title }}</h3>
                            <p>{!! str_replace("\n", '<br>', substr($announcement->announcement, 0, 100) . '...') !!}</p>
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
        </div>
    @endif

</x-app-layout>
