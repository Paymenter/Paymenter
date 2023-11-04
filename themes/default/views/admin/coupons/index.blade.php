<x-admin-layout title="Coupons">
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Coupons') }}</h1>
    <div class="flex justify-end pr-3 pt-3 mb-4">
        <a href="{{ route('admin.coupons.store') }}">
            <button class="button button-primary">
                {{ __('Create') }}
            </button>
        </a>
    </div>
    <livewire:admin.coupons />
</x-admin-layout>
