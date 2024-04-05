<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext text-center">{{ __('Tickets') }}</h1>
    <div class="flex items-center justify-end mt-4">
        <a href="{{ route('admin.tickets.create') }}" class="mr-4 button button-success">
            <i class="ri-add-fill"></i> <span>{{ __('Create') }}</span>
        </a>
    </div>

    <h2 class="text-2xl font-bold dark:text-darkmodetext mb-2">{{ __('Open Tickets') }}</h2>
    <livewire:admin.tickets status="open" :key="'open'" />
    <h2 class="text-2xl font-bold dark:text-darkmodetext mt-4 mb-2">{{ __('Closed Tickets') }}</h2>
    <livewire:admin.tickets status="closed" :key="'closed'" />
</x-admin-layout>
