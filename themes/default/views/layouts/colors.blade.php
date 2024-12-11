<style>
    :root {
        /* Branding Colors (Light) */
        --color-primary: {{ theme('primary', '229 100% 64%') }};
        --color-secondary: {{ theme('secondary', '0 0% 92%') }};

        /* Neutral Colors - Borders, Accents... (Light) */
        --color-neutral: {{ theme('neutral', '220 25% 85%') }};

        /* Text Colors (Light) */
        --color-base: {{ theme('base', '0 0% 0%') }};
        --color-muted: {{ theme('muted', '220 28% 25%') }};
        --color-inverted: {{ theme('inverted', '100 100% 100%') }};

        /* State Colors */
        --color-success: {{ theme('success', '142 71% 45%') }};
        --color-error: {{ theme('error', '0 75% 60%') }};
        --color-warning: {{ theme('warning', '25 95% 53%') }};
        --color-inactive: {{ theme('inactive', '0 0% 63%') }};
        --color-info: {{ theme('info', '210 100% 60%') }};

        /* Background Colors (Light) */
        --color-background-secondary: {{ theme('background-secondary', '0 0% 95%') }};
        --color-background: {{ theme('background', '100 100% 100%') }};
    }

    html.dark {
        /* Branding Colors (Dark) */
        --color-primary: {{ theme('dark-primary', '229 100% 64%') }};
        --color-secondary: {{ theme('dark-secondary', '222 32% 15%') }};

        /* Neutral Colors - Borders, Accents... (Dark) */
        --color-neutral: {{ theme('dark-neutral', '220 25% 29%') }};

        /* Text Colors (Dark) */
        --color-base: {{ theme('dark-base', '100 100% 100%') }};
        --color-muted: {{ theme('dark-muted', '220 28% 25%') }};
        --color-inverted: {{ theme('dark-inverted', '220 14% 60%') }};

        /* Background Colors (Dark) */
        --color-background-secondary: {{ theme('dark-background-secondary', '222 32% 15%') }};
        --color-background: {{ theme('dark-background', '221 39% 11%') }};
    }
</style>