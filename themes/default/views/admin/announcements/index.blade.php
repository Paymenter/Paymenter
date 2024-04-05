<x-admin-layout>
    <x-slot name="title">
        {{ __('Announcements') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Announcements') }}</h1>
    <div class="flex justify-end pr-3 pt-3 mb-4">
        <a href="{{ route('admin.announcements.store') }}">
            <button class="button button-primary">
                {{ __('Create') }}
            </button>
        </a>
    </div>
    <livewire:admin.announcements />
</x-admin-layout>
