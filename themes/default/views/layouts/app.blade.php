<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{darkMode: $persist(false)}" :class="{'dark': darkMode}" class="antialiased">

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

<body class="w-full bg-background text-base min-h-screen flex flex-col">
    {!! hook('body') !!}
    <x-navigation />
    <div class="w-full flex flex-grow">
        @if (request()->routeIs('dashboard', 'services', 'services.*', 'invoices', 'invoices.*', 'tickets', 'tickets.*', 'account'))
            <x-navigation.sidebar title="$title" />
        @endif
        <div class="{{ request()->routeIs('dashboard', 'services', 'services.*', 'invoices', 'invoices.*', 'tickets', 'tickets.*', 'account') ? 'md:ml-64' : '' }} flex flex-col flex-grow">
            <main class="container mt-24 mx-auto">
                {{ $slot }}
            </main>
            <x-notification />
            <div class="py-8">
                <x-navigation.footer />
            </div>
        </div>
    </div>
    {!! hook('footer') !!}
</body>

</html>
