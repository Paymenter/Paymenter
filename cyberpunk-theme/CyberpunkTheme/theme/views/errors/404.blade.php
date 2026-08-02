<x-app-layout>
    <x-slot name="title">
        {{ __('errors.404.title') }}
    </x-slot>

    <div class="container flex flex-col items-center justify-center text-center py-20">
        <p class="text-base font-semibold text-primary">404</p>
        <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance sm:text-7xl">
            {{ __('errors.404.title') }}
        </h1>
        <p class="mt-6 text-lg font-medium text-pretty text-base/50 sm:text-xl/8">
            {{ __('errors.404.message') }}
        </p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="{{ route('home') }}" wire:navigate>
                <x-button.primary>
                    {{ __('errors.404.return_home') }}
                </x-button.primary>
            </a>
        </div>
    </div>
</x-app-layout>