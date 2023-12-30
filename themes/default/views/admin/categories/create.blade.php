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
            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                @csrf
                <x-input type="text" name="name" id="name" :value="old('name')" :placeholder="__('Name')" required class="mt-4" :label="__('Name')" />

                <x-input type="textarea" name="description" :value="old('description')" :placeholder="__('Description')" required :label="__('Description')" />

                <x-input type="slug" name="slug" :value="old('slug')" :placeholder="__('Slug')" required  :label="__('Slug')" />

                <x-input type="file" name="image" :value="old('image')" :placeholder="__('Image')" :label="__('Image')" id="image" />
    
                <x-input type="select" name="parent_id" :value="old('parent_id')" :placeholder="__('Parent')" :label="__('Parent')">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-input>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="inline-flex justify-center w-max float-right button button-primary">
                        {{ __('Create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
