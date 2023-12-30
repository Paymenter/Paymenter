<x-admin-layout>
    <x-slot name="title">
        {{ __('Categories') }}
    </x-slot>
    <div class="p-3 bg-white dark:bg-secondary-100 flex flex-row justify-between">
        <div>
            <div class="mt-3 text-2xl font-bold dark:text-darkmodetext">
                {{ __('Categories') }}
            </div>
            <div class="mt-3 text-gray-500 dark:text-darkmodetext">
                {{ __('Here you can see all categories.') }}
            </div>
        </div>
        <div class="flex my-auto float-end justify-end mr-4">
            <a href="{{ route('admin.categories.store') }}"
               class="button button-primary">
                <i class="ri-add-line"></i> {{ __('Create') }}
            </a>
        </div>
    </div>
    <livewire:admin.categories />

</x-admin-layout>
