<x-admin-layout title="Configurable Options">
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Configurable Options') }}</h1>
    <div class="flex justify-end pr-3 pt-3">
        <a href="{{ route('admin.configurable-options.create') }}">
            <button class="button button-primary">
                {{ __('Create') }}
            </button>
        </a>
    </div>
    <livewire:admin.configurable-options />
</x-admin-layout>