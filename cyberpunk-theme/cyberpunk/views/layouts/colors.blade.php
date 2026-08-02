@php
$hsl = fn ($value) => str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', $value));
$fonts = [
    'orbitron' => "'Orbitron', ui-sans-serif, system-ui, sans-serif",
    'rajdhani' => "'Rajdhani', ui-sans-serif, system-ui, sans-serif",
    'share-tech' => "'Share Tech Mono', ui-monospace, monospace",
];
// Este parcial también se renderiza dentro del panel de administración,
// por lo que comprobamos que los helpers del tema estén disponibles.
$hasHelpers = function_exists('cyber_cfg');
$font = $hasHelpers ? cyber_cfg('font_family', 'system') : theme('font_family', 'system');
$rawBackground = $hasHelpers ? cyber_cfg('background_image') : theme('background_image');
$backgroundImage = $hasHelpers ? cyber_media($rawBackground) : $rawBackground;
$overlay = max(0, min(100, (int) theme('background_overlay', 80)));
@endphp

@if(isset($fonts[$font]))
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family={{ $font === 'orbitron' ? 'Orbitron:wght@400;600;800' : ($font === 'rajdhani' ? 'Rajdhani:wght@400;600;700' : 'Share+Tech+Mono') }}&display=swap" rel="stylesheet">
@endif

<style>
    :root {
        /* Branding Colors (Light) */
        --color-primary: {{ $hsl(theme('primary', '330 100% 50%')) }};
        --color-secondary: {{ $hsl(theme('secondary', '352 96% 52%')) }};
        --color-accent: {{ $hsl(theme('accent', '288 96% 60%')) }};

        /* Neutral Colors - Borders, Accents... (Light) */
        --color-neutral: {{ $hsl(theme('neutral', '330 40% 86%')) }};

        /* Text Colors (Light) */
        --color-base: {{ $hsl(theme('base', '330 20% 8%')) }};
        --color-muted: {{ $hsl(theme('muted', '330 10% 45%')) }};
        --color-inverted: {{ $hsl(theme('inverted', '0 0% 100%')) }};

        /* State Colors */
        --color-success: 152 76% 45%;
        --color-error: 0 85% 60%;
        --color-warning: 38 95% 55%;
        --color-inactive: 0 0% 55%;
        --color-info: 199 95% 58%;

        /* Background Colors (Light) */
        --color-background: {{ $hsl(theme('background', '330 40% 99%')) }};
        --color-background-secondary: {{ $hsl(theme('background-secondary', '330 45% 96%')) }};

        @if(isset($fonts[$font]))
        --font-cyber-display: {!! $fonts[$font] !!};
        @endif
    }

    .dark {
        /* Branding Colors (Dark) */
        --color-primary: {{ $hsl(theme('dark-primary', '330 100% 55%')) }};
        --color-secondary: {{ $hsl(theme('dark-secondary', '352 100% 55%')) }};
        --color-accent: {{ $hsl(theme('dark-accent', '288 100% 65%')) }};

        /* Neutral Colors - Borders, Accents... (Dark) */
        --color-neutral: {{ $hsl(theme('dark-neutral', '330 45% 20%')) }};

        /* Text Colors (Dark) */
        --color-base: {{ $hsl(theme('dark-base', '0 0% 100%')) }};
        --color-muted: {{ $hsl(theme('dark-muted', '330 20% 68%')) }};
        --color-inverted: {{ $hsl(theme('dark-inverted', '0 0% 100%')) }};

        /* Background Colors (Dark) */
        --color-background: {{ $hsl(theme('dark-background', '0 0% 4%')) }};
        --color-background-secondary: {{ $hsl(theme('dark-background-secondary', '330 30% 8%')) }};
    }

    @if($backgroundImage)
    .cyber-bg-image {
        background-image: url('{{ $backgroundImage }}');
    }
    .cyber-bg-overlay {
        background: hsl(var(--color-background) / {{ $overlay / 100 }});
    }
    @endif
</style>
