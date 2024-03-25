<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['themes/' . config('settings.theme') . '/js/app.js'], config('settings.theme'))
    </head>
    <body class="w-full bg-primary-100 dark:bg-primary-900">
        <main>
            {{ $slot }}
        </main>
    </body>
</html>
