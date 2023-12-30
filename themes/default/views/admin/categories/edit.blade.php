<x-admin-layout>
    <x-slot name="title">
        {{ __('Categories Edit') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Categories Edit') }}</h1>
    <x-auth-validation-errors class="mb-4" :errors="$errors" />


    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <x-input type="text" name="name" id="name" :value="old('name', $category->name)" :placeholder="__('Name')" required class="mt-4" :label="__('Name')" />
        
        <x-input type="textarea" name="description" :value="old('description', $category->description)" :placeholder="__('Description')" required :label="__('Description')" />
       
        <x-input type="text" name="slug" :value="old('slug', $category->slug)" :placeholder="__('Slug')" required  :label="__('Slug')" />

        <x-input type="file" name="image" :value="old('image', $category->image)" :placeholder="__('Image')" :label="__('Image')" id="image" class="mt-2" />
        <x-input type="checkbox" name="remove_image" :value="old('remove_image')" :placeholder="__('Remove Image')" :label="__('Remove Image')" id="remove_image"  class="mt-1" />

        <x-input type="select" name="parent_id" :placeholder="__('Parent')" :label="__('Parent')">
            <option value="">{{ __('None') }}</option>
            @foreach ($categories as $category2)
                <option value="{{ $category2->id }}" @if ($category2->id == old('parent_id', $category->category_id)) selected @endif>{{ $category2->name }}</option>
            @endforeach
        </x-input>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="inline-flex justify-center w-max float-right button button-primary">
                {{ __('Update') }}
            </button>
        </div>
    </form>

</x-admin-layout>
