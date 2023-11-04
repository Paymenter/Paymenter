<x-admin-layout title="roles">
    <h1 class="text-center text-2xl font-bold">{{ __('Roles') }}</h1>
        <!-- right top aligned button -->
    <div class="flex justify-end pr-3 pt-3 mb-4">
        <a href="{{ route('admin.roles.create') }}">
            <button class="button button-primary">
                {{ __('Create') }}
            </button>
        </a>
    </div>
    <livewire:admin.roles />
</x-admin-layout>
