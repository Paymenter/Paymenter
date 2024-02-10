<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Software build by https://paymenter.org -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        if ("{{ config('settings::theme:snow') }}" == 1) {
            document.addEventListener("DOMContentLoaded", function() {
                window.snow();
            });
        }
        window.addEventListener('keydown', function(e) {
            var ctrlDown = true;
            var ctrlKey = 17,
                enterKey = 13;
            $(document).keydown(function(e) {
                if (e.keyCode == ctrlKey) ctrlDown = true;
                if (e.keyCode == enterKey && ctrlDown) {
                    if ($('#submit').length) {
                        $('#submit').click();
                    }
                }
            }).keyup(function(e) {
                if (e.keyCode == ctrlKey) ctrlDown = false;
            });
        });
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        .snow {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            pointer-events: none;
        }

        :root {
            --secondary-50: {{ config('settings::theme:secondary-50', '#ffffff') }};
            --secondary-100: {{ config('settings::theme:secondary-100', '#fafcff') }};
            --secondary-200: {{ config('settings::theme:secondary-200', '#ebeef3') }};
            --secondary-300: {{ config('settings::theme:secondary-300', '#bbbfd2') }};
            --secondary-400: {{ config('settings::theme:secondary-400', '#808498') }};
            --secondary-500: {{ config('settings::theme:secondary-500', '#606372') }};
            --secondary-600: {{ config('settings::theme:secondary-600', '#4d4f60') }};
            --secondary-700: {{ config('settings::theme:secondary-700', '#353741') }};
            --secondary-800: {{ config('settings::theme:secondary-800', '#1c1c20') }};
            --secondary-900: {{ config('settings::theme:secondary-900', '#000000') }};

            --primary-50: {{ config('settings::theme:primary-50', '#EDF0FF') }};
            --primary-100: {{ config('settings::theme:primary-100', '#C6DBFF') }};
            --primary-200: {{ config('settings::theme:primary-200', '#9BBEFB') }};
            --primary-300: {{ config('settings::theme:primary-300', '#799CD8') }};
            --primary-400: {{ config('settings::theme:primary-400', '#5270FD') }};
        }

        .dark {
            --secondary-50: {{ config('settings::theme:secondary-50-dark', '#1E202D') }};
            --secondary-100: {{ config('settings::theme:secondary-100-dark', '#313441') }};
            --secondary-200: {{ config('settings::theme:secondary-200-dark', '#404351') }};
            --secondary-300: {{ config('settings::theme:secondary-300-dark', '#4F525E') }};
            --secondary-400: {{ config('settings::theme:secondary-400-dark', '#656874') }};
            --secondary-500: {{ config('settings::theme:secondary-500-dark', '#7D8091') }};
            --secondary-600: {{ config('settings::theme:secondary-600-dark', '#AEB2C2') }};
            --secondary-700: {{ config('settings::theme:secondary-700-dark', '#CACBD2') }};
            --secondary-800: {{ config('settings::theme:secondary-800-dark', '#F1F1F1') }};
            --secondary-900: {{ config('settings::theme:secondary-900-dark', '#ffffff') }};
        }
    </style>
    @empty($title)
        <title>{{ config('app.name', 'Paymenter') }}</title>
    @else
        <title>{{ config('app.name', 'Paymenter') . ' - ' . ucfirst($title) }}</title>
    @endempty

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

    @vite(['themes/' . config('settings::theme-active') . '/js/app.js', 'themes/' . config('settings::theme-active') . '/css/app.css'], config('settings::theme-active'))

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    @if (config('settings::app_logo'))
        <link rel="icon" href="{{ asset(config('settings::app_logo')) }}" type="image/png">
    @else
        <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    @endif
    <meta content="{{ ucfirst($title) ?? config('settings::seo_title') }}" property="og:title">
    <meta content="{{ $description ?? config('settings::seo_description') }}" property="og:description">
    <meta content="{{ $description ?? config('settings::seo_description') }}" name="description">
    <meta content='{{ $image ?? config('settings::seo_image') }}' property='og:image'>
    <link type="application/json+oembed"
        href="{{ url('/') }}/manifest.json?title={{ config('app.name', 'Paymenter') }}&author_url={{ url('/') }}&author_name={{ config('app.name', 'Paymenter') }}" />
    <meta name="twitter:card" content="@if (config('settings::seo_twitter_card')) summary_large_image @endif">
    <meta name="theme-color" content="#5270FD">
</head>

<body class="bg-secondary-100 dark:bg-secondary-50 text-secondary-700">
    <div id="app" class="flex flex-col h-full w-full overflow-x-hidden">
        <x-paymenter-update />
        <!-- @if (!$clients || config('settings::sidebar') == 0) -->
            @include('layouts.navigation')
        <!-- @endif -->
            <main class="w-full mt-4 md:px-16 flex flex-col h-full">
                    {{ $slot }}
            </main>
    </div>
</body>

</html>
