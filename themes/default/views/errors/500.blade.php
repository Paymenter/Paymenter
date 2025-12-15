<x-app-layout>
    <x-slot name="title">
        {{ __('errors.500.title') }}
    </x-slot>

    <div class="container flex flex-col items-center justify-center text-center py-20">
        <p class="text-base font-semibold text-indigo-400">500</p>
        <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance sm:text-7xl">
            {{ __('errors.500.title') }}
        </h1>
        <p class="mt-6 text-lg font-medium text-pretty text-base/50 sm:text-xl/8">
            {{ __('errors.500.message') }}
        </p>
    </div>
</x-app-layout>