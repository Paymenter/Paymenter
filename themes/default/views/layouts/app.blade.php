<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ config('app.name', 'Paymenter') }}
        @isset($title)
            - {{ $title }}
        @endisset
    </title>
    @vite(['themes/' . config('settings.theme') . '/js/app.js'], config('settings.theme'))
    @include('layouts.colors')

    @if (config('settings.logo'))
        <link rel="icon" href="{{ Storage::url(config('settings.logo')) }}" type="image/png">
    @endif
    {!! hook('head') !!}
</head>

<body class="w-full bg-primary-900 text-white min-h-screen flex flex-col">
    {!! hook('body') !!}
    <x-navigation />
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        {{ $slot }}
    </main>
    <x-navigation.footer />
    <x-notification />
    {!! hook('footer') !!}
</body>

</html>
