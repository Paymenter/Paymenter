<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        @vite(['themes\default/css/app.css', 'themes\default/js/app.js'], 'default')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>
        <div id="app" class="font-sans antialiased text-gray-900">
            {{ $slot }}
        </div>
    </body>
</html>
