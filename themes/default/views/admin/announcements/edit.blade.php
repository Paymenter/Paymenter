<x-admin-layout>
    <x-slot name="title">
        {{ __('Announcement Edit') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Announcement Edit') }}</h1>

    <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
        @csrf
        <div class="mt-4">
            <label class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                {{ __('Title') }}
            </label>
            <input class="form-input w-full" type="text" name="title" value="{{ $announcement->title }}">
        </div>
        <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
        <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
        <div class="mt-4">
            <label class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                {{ __('Description') }}
            </label>
            <textarea id="announcement" type="text" class="form-input w-full @error('announcement') border-red-500 @enderror"
                name="announcement" value="{{ old('description', $announcement->announcement) }}" required
                autocomplete="announcement" autofocus>{{ $announcement->announcement }}</textarea>
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
            <input id="published" type="checkbox" class="form-input w-fit" name="published"
                {{ $announcement->published ? 'checked' : '' }}>
            <label for="published" class="ml-2 block text-sm text-gray-900 dark:text-darkmodetext">
                {{ __('Published') }}
            </label>
        </div>
        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="inline-flex justify-center w-max float-right button button-primary">
                {{ __('Update') }}
            </button>
        </div>
    </form>
</x-admin-layout>
