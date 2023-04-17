<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Software build by https://paymenter.org -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Admin - ' . $title }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    @vite('resources/js/app.js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
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
    </script>
</head>

<body class="font-sans antialiased">
    <div id="app" class="min-h-screen bg-gray-100 dark:bg-darkmode">
        @if (config('settings::sidebar') == 1)
            @include('layouts.adminsidenavigation')
        @else
            @include('layouts.adminnavigation')
        @endif
        <main class="grow">
            @if (!request()->routeIs('admin.index') && !request()->routeIs('admin.settings*'))
                <div class="py-12">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="overflow-hidden dark:bg-darkmode2 bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6 dark:bg-darkmode2 bg-white border-b border-gray-200 dark:border-gray-800 md:p-12 dark:text-darkmodetext text-gray-800">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{ $slot }}
            @endif
        </main>
        <footer>
            <div class="flex flex-col justify-center items-center dark:text-white dark:bg-darkmode">
                <!-- Please do not remove the credits. -->
                <a class="text-gray-500 dark:text-gray-400 text-sm" href="https://paymenter.org">Paymenter &copy; 2022 -
                    {{ date('Y') }}</a>
            </div>
        </footer>
    </div>
</body>

</html>
