<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Software build by https://paymenter.org -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            if ("{{ config('settings::snow') }}" == 1) {
                var canvas, ctx;
                var points = [];
                var maxDist = 1000;

                function init() {
                    canvas = document.getElementById("snow");
                    ctx = canvas.getContext("2d");
                    resizeCanvas();
                    pointFun();
                    setInterval(pointFun, 20);
                    window.addEventListener('resize', resizeCanvas, false);
                }

                function point() {
                    this.x = Math.random() * (canvas.width + maxDist) - (maxDist / 2);
                    this.y = Math.random() * (canvas.height + maxDist) - (maxDist / 2);
                    this.z = (Math.random() * 0.5) + 0.5;
                    this.vx = ((Math.random() * 2) - 0.5) * this.z;
                    this.vy = ((Math.random() * 1.5) + 0.5) * this.z;
                    this.fill = "rgba(108, 122, 137," + ((0.4 * Math.random()) + 0.5) + ")";
                    this.dia = ((Math.random() * 2.5) + 1.5) * this.z;
                    this.vs = Math.floor(Math.random() * (25 - 15 + 1) + 15);
                    points.push(this);
                }

                function generatePoints(amount) {
                    var temp;
                    for (var i = 0; i < amount; i++) {
                        temp = new point();
                    }
                }

                function draw(obj) {
                    ctx.beginPath();
                    ctx.strokeStyle = "transparent";
                    ctx.fillStyle = obj.fill;
                    ctx.arc(obj.x, obj.y, obj.dia, 0, 2 * Math.PI);
                    ctx.closePath();
                    ctx.stroke();
                    ctx.fill();
                }

                function drawSnowflake(obj) {
                    var snowflake = new Image();
                    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                            '(prefers-color-scheme: dark)').matches)) {
                        snowflake.src = 'https://www.platinumhost.io/snowflake.svg';
                    } else {
                        snowflake.src = 'https://www.platinumhost.io/snowflake_dark.svg';
                    }
                    ctx.drawImage(snowflake, obj.x, obj.y * Math.PI, obj.vs, obj.vs);
                }

                function update(obj) {
                    obj.x += obj.vx;
                    obj.y += obj.vy;
                    if (obj.x > canvas.width + (maxDist / 2)) {
                        obj.x = -(maxDist / 2);
                    } else if (obj.xpos < -(maxDist / 2)) {
                        obj.x = canvas.width + (maxDist / 2);
                    }
                    if (obj.y > canvas.height + (maxDist / 2)) {
                        obj.y = -(maxDist / 2);
                    } else if (obj.y < -(maxDist / 2)) {
                        obj.y = canvas.height + (maxDist / 2);
                    }
                }

                function pointFun() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    for (var i = 0; i < points.length; i++) {
                        drawSnowflake(points[i]);
                        draw(points[i]);
                        update(points[i]);
                    };
                }

                function resizeCanvas() {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                    points = [];
                    generatePoints(window.innerWidth / 3);
                    pointFun();
                }
                window.onload = init;
            }
        })();
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
        <title>{{ config('app.name', 'Paymenter') . ' - ' . $title }}</title>
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
        @if (config('settings::sidebar') == 1)
            @include('layouts.sidenavigation')
        @else
            @include('layouts.navigation')
        @endif
        <!-- Page Content -->
        <main class="grow">
            {{ $slot }}
        </main>
        <footer class="pt-5 pb-3">
            <div class="content text-center text-secondary-600 text-sm">
                <a href="https://paymenter.org">
                    Paymenter &copy; 2022 - {{ date('Y') }}
                </a>
            </div>
        </footer>
    </div>
</body>

</html>
