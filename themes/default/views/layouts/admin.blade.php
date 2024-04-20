<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@isset($title) {{ $title }} - @endisset {{ config('app.name', 'Paymenter') }}</title>
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, preventDefault }) => {
                        if (status === 419) {
                            confirm('Your custom page expiration behavior...');
         
                            preventDefault();
                        }
                        if (status === 403) {
                            alert('403 Error Happened');
         
                            preventDefault();
                        }
        
                    })
                })
            })
        </script>
        @vite(['themes/' . config('settings.theme') . '/js/app.js'], config('settings.theme'))
        @include('layouts.colors')
        
        <link rel="icon" href="{{ asset(config('settings.logo')) }}" type="image/png">
    </head>
    <body class="w-full bg-primary-900">
        <x-navigation.admin />
        <!-- Allow for sidebar -->
        <main class="min-h-[calc(100vh-92px)] p-8">
            <div class="flex flex-col gap-2 shadow-sm p-6 bg-primary-800 rounded-md text-white">
                @isset($title)
                    <h1 class="text-xl">{{ $title }}</h1>
                @endisset
                {{ $slot }}
            </div>
        </main>
        <x-navigation.admin-footer />
    </body>
</html>
