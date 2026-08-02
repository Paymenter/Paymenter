@php
$fxClasses = collect([
    cyber_bool('effect_neon', true) ? 'cyber-fx-neon' : null,
    cyber_bool('effect_scanlines', true) ? 'cyber-fx-scanlines' : null,
    cyber_bool('effect_grid', true) ? 'cyber-fx-grid' : null,
    cyber_bool('effect_glitch', true) ? 'cyber-fx-glitch' : null,
    cyber_bool('effect_noise', false) ? 'cyber-fx-noise' : null,
])->filter()->implode(' ');
$backgroundImage = cyber_media(cyber_cfg('background_image'));

// Si los assets compilados no están en public/<tema>/ no podemos llamar a @vite:
// lanzaría una excepción y tumbaría el sitio entero. Mejor avisar.
$cyberTheme = config('settings.theme', 'cyberpunk');
$cyberHasAssets = file_exists(public_path('hot'))
    || file_exists(public_path($cyberTheme . '/manifest.json'))
    || file_exists(public_path($cyberTheme . '/.vite/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $fxClasses }}" @if(in_array(app()->getLocale(), config('app.rtl_locales'))) dir="rtl" @endif>

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
    @livewireStyles
    @if($cyberHasAssets)
    @vite(['themes/' . config('settings.theme') . '/js/app.js', 'themes/' . config('settings.theme') . '/css/app.css'], config('settings.theme'))
    @endif
    @include('layouts.colors')

    @if (config('settings.favicon'))
    <link rel="icon" href="{{ Storage::url(config('settings.favicon')) }}">
    @endif
    @isset($title)
    <meta content="{{ isset($title) ? config('app.name', 'Paymenter') . ' - ' . $title : config('app.name', 'Paymenter') }}" property="og:title">
    <meta content="{{ isset($title) ? config('app.name', 'Paymenter') . ' - ' . $title : config('app.name', 'Paymenter') }}" name="title">
    @endisset
    @isset($description)
    <meta content="{{ $description }}" property="og:description">
    <meta content="{{ $description }}" name="description">
    @endisset
    @isset($image)
    <meta content="{{ $image }}" property="og:image">
    <meta content="{{ $image }}" name="image">
    @endisset

    <meta name="theme-color" content="{{ theme('dark-primary', theme('primary')) }}">

    {!! hook('head') !!}
</head>

<body class="w-full bg-background text-base min-h-screen flex flex-col antialiased relative"
    x-cloak
    x-data="{
        theme: $persist('dark').as('theme_mode'),
        systemDark: window.matchMedia('(prefers-color-scheme: dark)').matches,
        init() {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                this.systemDark = e.matches;
            });
        },
        get isDark() {
            return this.theme === 'dark' || (this.theme === 'system' && this.systemDark);
        }
    }"
    :class="{'dark': isDark}"
>
    @if($backgroundImage)
    <div class="cyber-bg-image" aria-hidden="true"></div>
    <div class="cyber-bg-overlay" aria-hidden="true"></div>
    @endif
    @if(cyber_bool('effect_noise', false))
    <div class="cyber-noise-layer" aria-hidden="true"></div>
    @endif

    <x-cyber.animations />

    @unless($cyberHasAssets)
    <div style="position:fixed;inset:0 0 auto 0;z-index:9999;background:#7f1d1d;color:#fff;padding:14px 18px;font:14px/1.5 system-ui,sans-serif">
        <strong>Faltan los estilos del tema Cyberpunk.</strong>
        No existe <code>public/{{ $cyberTheme }}/manifest.json</code>, por eso la web se ve sin diseño.
        Copia la carpeta <code>public/</code> del paquete a <code>public/{{ $cyberTheme }}/</code>,
        o ejecuta <code>npm run build {{ $cyberTheme }}</code> en la raíz de Paymenter.
    </div>
    <div style="height:64px"></div>
    @endunless

    {!! hook('body') !!}
    <x-navigation />
    <div class="w-full flex flex-grow relative z-1">
        @if (isset($sidebar) && $sidebar)
        <x-navigation.sidebar title="$title" />
        @endif
        <div class="{{ (isset($sidebar) && $sidebar) ? 'md:ml-64 rtl:ml-0 rtl:md:mr-64' : '' }} flex flex-col flex-grow overflow-auto">
            <main class="mt-16 grow">
                {{ $slot }}
            </main>
            <x-notification />
            <x-confirmation />
            <div class="flex">
                <x-navigation.footer />
            </div>
        </div>
        <x-impersonating />
    </div>
    @livewireScriptConfig
    {!! hook('footer') !!}
</body>

</html>
