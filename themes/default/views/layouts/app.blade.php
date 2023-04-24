<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Software build by https://paymenter.org -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        if ("{{ config('settings::snow') }}" == 1) {
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
    </style>
    @isset($title)
        <title>{{ config('app.name', 'Paymenter') . ' - ' . ucfirst($title) }}</title>
    @else
        <title>{{ config('app.name', 'Paymenter') }}</title>
    @endisset

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700&display=swap">

    @vite('resources/js/app.js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta content="{{ config('settings::seo_title') }}" property="og:title">
    <meta content="{{ config('settings::seo_description') }}" property="og:description">
    <meta content="{{ config('settings::seo_description') }}" name="description">
    <meta content='{{ config('settings::seo_image') }}' property="og:image">
    <link type="application/json+oembed"
        href="{{ url('/') }}/manifest.json?title={{ config('app.name', 'Paymenter') }}&author_url={{ url('/') }}&author_name={{ config('app.name', 'Paymenter') }}" />
    <meta name="twitter:card" content="@if (config('settings::seo_twitter_card')) summary_large_image @endif">
    <meta name="theme-color" content="#5270FD">
</head>

<body class="bg-secondary-100 dark:bg-secondary-50 text-secondary-700 font-sans">
    <canvas class="snow" id="snow" width="1920" height="1080"></canvas>
    <div id="app" class="min-h-screen">
        @if (!$clients || config('settings::sidebar') == 0)
            @include('layouts.navigation')
        @endif
        <div class="@if (config('settings::sidebar') == 1) flex md:flex-nowrap flex-wrap @endif">
            @if ($clients)
                @include('layouts.subnavigation')
            @endif
            <div class="w-full flex flex-col min-h-[calc(100vh-60px)]">

                <main class="grow">
                    {{ $slot }}
                </main>

                <footer class="pt-5 pb-3 mt-auto">
                    <div class="content text-center text-secondary-600 text-sm">
                        <a href="https://paymenter.org">
                            Paymenter &copy; 2022 - {{ date('Y') }}
                        </a>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</body>

</html>
