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
                <div class="mt-4">
                    <label class="block dark:text-darkmodetext text-sm font-medium text-gray-700">
                        {{ __('Title') }}
                    </label>
                    <input class="form-input w-full" type="text" name="title" value="{{ old('title') }}">
                </div>
                <div class="mt-4">
                    <label class="block dark:text-darkmodetext text-sm font-medium text-gray-700">
                        {{ __('Description') }}
                    </label>
                    <textarea id="announcement" type="text" class="form-input w-full @error('announcement') border-red-500 @enderror"
                        name="announcement" value="{{ old('description') }}" required
                        autocomplete="announcement" autofocus></textarea>
                </div>
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
                <div class="flex items-center">
                    <input id="published" type="checkbox" class="form-input w-fit" name="published" value="1">
                    <label for="published" class="ml-2 block text-sm text-gray-900 dark:text-darkmodetext">
                        {{ __('Published') }}
                    </label>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
