<x-admin-layout>
    <x-slot name="title">
        {{ __('Clients') }}
    </x-slot>
    <div class="p-3 bg-white dark:bg-secondary-100 flex flex-row justify-between">
        <div>
            <div class="mt-3 text-2xl font-bold dark:text-darkmodetext">
                {{ __('All clients') }}
            </div>
            <div class="mt-3 text-gray-500 dark:text-darkmodetext">
                {{ __('Here you can see all your clients.') }}
            </div>
        </div>
        <div class="flex my-auto float-end justify-end mr-4">
            <a href="{{ route('admin.clients.create') }}"
               class="px-4 py-2 font-bold text-white transition rounded delay-400 bg-blue-500 button button-primary">
                <i class="ri-user-add-line"></i> {{ __('Create client') }}
            </a>
        </div>
    </div>
    <livewire:admin.clients />
</x-admin-layout>
