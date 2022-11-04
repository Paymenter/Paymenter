<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @isset($title)
        <title>{{ config('app.name', 'Paymenter') . ' - ' . $title }}</title>
    @else
        <title>{{ config('app.name', 'Paymenter') }}</title>
    @endisset

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    @vite(['themes/default/css/app.css', 'themes/default/js/app.js'], 'default')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    @php $seo = App\Models\Settings::first() @endphp
    <meta content="@isset($seo->seo_title){{ $seo->seo_title }}@endisset" property="og:title">
    <meta content="@isset($seo->seo_description){{ $seo->seo_description }}@endisset" property="og:description">
    <meta content='@isset ($seo->seo_image){{ $seo->seo_image }}@endisset' property="og:image">
    <link type="application/json+oembed" href="{{ url('/') }}/manifest.json?title={{ urldecode('Paymenter') }}&author_url={{ urldecode('https://discord.gg/xB4UUT3XQg') }}&author_name=demo"/>
    <meta name="twitter:card" content="@isset($seo->seo_twitter_card)summary_large_image @endisset">
    <meta name="theme-color" content="#5270FD">
</head>

<body class="font-sans antialiased bg-gray-100">
    <div id="app" class="dark:text-white min-h-screen dark:bg-darkmode">
        @if (App\Models\Settings::first()->navbar == '1')
            @include('layouts.navigation')
        @else
            @include('layouts.sidenavigation')
        @endif
        <!-- Page Content -->
        <main class="grow">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
