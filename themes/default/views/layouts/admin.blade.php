<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Admin - ' . $title }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    @vite(['themes/default/css/app.css', 'themes/default/js/app.js'], 'default')
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
        window.addEventListener('keydown', function (e) {
        	var ctrlDown = true;
        	var ctrlKey = 17, enterKey = 13;
        	$(document).keydown(function(e) {
        	    if (e.keyCode == ctrlKey) ctrlDown = true;
        	    if (e.keyCode == enterKey && ctrlDown) {
					if ($('#submit').length) {
						$('#submit').click();
					} else {
						console.log("Doesn't exist");
					}
        	        return false;
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
            {{ $slot }}
        </main>
    </div>
</body>

</html>
