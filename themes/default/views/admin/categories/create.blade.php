<x-admin-layout>
    <x-slot name="title">
        {{ __('Categories') }}
    </x-slot>
    <!-- create category -->
    <div class="flex flex-wrap">
        <div class="w-full">
            <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Create category') }}</h1>
        </div>

        <div class="w-full">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="mt-4">
                    <label class="block dark:text-darkmodetext text-sm font-medium text-gray-700">
                        {{ __('Name') }}
                    </label>
                    <input id="name" type="text"
                        class="form-input w-full @error('name') border-red-500 @enderror" name="name"
                        value="{{ old('name') }}" required autocomplete="name" autofocus>
                </div>
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                        {{ __('Description') }}
                    </label>
                    <textarea id="description" type="text" class="form-input w-full @error('description') border-red-500 @enderror"
                        name="description" value="{{ old('description') }}" required autocomplete="description" autofocus></textarea>
                </div>
                <!-- slug -->
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                        {{ __('Slug') }}
                    </label>
                    <p>
                        <span class="text-gray-500" id="slugd"></span>
                    </p>
                    <input id="slug" type="text"
                        class="form-input w-full @error('slug') border-red-500 @enderror" name="slug"
                        value="{{ old('slug') }}" required autocomplete="slug" autofocus>
                </div>
                <script>
                    var slug = document.getElementById('slugd');
                    var name = document.getElementById('name');
                    slug.addEventListener('keyup', function() {
                        document.getElementById('slugd').textContent = '/category/' + slug.value;
                    });
                    name.addEventListener('keyup', function() {
                        slug.value = name.value.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                        document.getElementById('slugd').textContent = '/category/' + name.value;
                    });
                </script>
                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
