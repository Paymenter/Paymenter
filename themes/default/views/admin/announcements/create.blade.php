<x-admin-layout>
    <x-slot name="title">
        {{ __('Announcement') }}
    </x-slot>
    <!-- create category -->
    <div class="flex flex-wrap">
        <div class="w-full">
            <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Create Announcement') }}</h1>
        </div>

        <div class="w-full">
            <form method="POST" action="{{ route('admin.announcements.store') }}">
                @csrf
                    <x-input class="mt-4" type="text" name="title" value="{{ old('title') }}" label="{{__('Title') }}"/>

                    
                    <x-input id="announcement" type="textarea" class="mt-4"
                        name="announcement" value="{{ old('description') }}" required label="{{__('Description') }}"/>

                <script>
                    var easyMDE = new EasyMDE({
                        element: document.getElementById("announcement"),
                        spellChecker: false,
                        toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "link",
                            "image", "table", "|", "preview", "side-by-side", "fullscreen", "|", "guide"
                        ]
                    });
                </script>
                <!-- Published -->

                    <x-input id="published" type="checkbox" class="mt-4" name="published" value="1" label="{{ __('Published') }}"/>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="inline-flex justify-center w-max float-right button button-primary">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
