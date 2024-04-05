<x-admin-layout>
    <x-slot name="title">
        {{ __('Invoices') }}
    </x-slot>
    <div class="p-3 bg-white dark:bg-secondary-100 flex flex-row justify-between">
        <div>
            <div class="mt-3 text-2xl font-bold dark:text-darkmodetext">
                {{ __('Invoices') }}
            </div>
            <div class="mt-3 text-gray-500 dark:text-darkmodetext">
                {{ __('Here you can see all invoices.') }}
            </div>
        </div>
        <div class="flex my-auto float-end justify-end mr-4">
            <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2 font-bold text-white transition rounded delay-400 button button-primary">
                <i class="ri-add-circle-line mt-2"></i> {{ __('Create') }}
            </a>
        </div>
    </div>
    <livewire:admin.invoices />
</x-admin-layout>
