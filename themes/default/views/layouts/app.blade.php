<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    @vite(['themes/' . config('settings.theme') . '/js/app.js', 'themes/' . config('settings.theme') . '/css/app.css'], config('settings.theme'))
    @include('layouts.colors')

    @if (config('settings.logo'))
        <link rel="icon" href="{{ Storage::url(config('settings.logo')) }}" type="image/png">
    @endif
    {!! hook('head') !!}
</head>

<body class="w-full bg-background text-base min-h-screen flex flex-col antialiased" x-data="{darkMode: $persist(window.matchMedia('(prefers-color-scheme: dark)').matches)}" :class="{'dark': darkMode}">
    {!! hook('body') !!}
    <x-navigation />
    <div class="w-full flex flex-grow">
        @if (isset($sidebar) && $sidebar)
            <x-navigation.sidebar title="$title" />
        @endif
        <div class="{{ (isset($sidebar) && $sidebar) ? 'md:ml-64' : '' }} flex flex-col flex-grow overflow-auto">
            <main class="container mt-24 mx-auto px-4 sm:px-6 md:px-8 lg:px-10">
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
