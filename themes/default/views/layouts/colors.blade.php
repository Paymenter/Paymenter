<style>
    :root {
        /* Branding Colors (Light) */
        --color-primary: {{ str_replace(',', '', theme('primary', '229 100% 64%')) }};
        --color-secondary: {{ str_replace(',', '', theme('secondary', '237 33% 60%')) }};

        /* Neutral Colors - Borders, Accents... (Light) */
        --color-neutral: {{ str_replace(',', '', theme('neutral', '220 25% 85%')) }};

        /* Text Colors (Light) */
        --color-base: {{ str_replace(',', '', theme('base', '0 0% 0%')) }};
        --color-muted: {{ str_replace(',', '', theme('muted', '220 28% 25%')) }};
        --color-inverted: {{ str_replace(',', '', theme('inverted', '100 100% 100%')) }};

        /* State Colors */
        --color-success: 142 71% 45%;
        --color-error: 0 75% 60%;
        --color-warning: 25 95% 53%;
        --color-inactive: 0 0% 63%;
        --color-info: 210 100% 60%;

        /* Background Colors (Light) */
        --color-background: {{ str_replace(',', '', theme('background', '100 100% 100%')) }};
        --color-background-secondary: {{ str_replace(',', '', theme('background-secondary', '0 0% 97%')) }};
    }

    html.dark {
        /* Branding Colors (Dark) */
        --color-primary: {{ str_replace(',', '', theme('dark-primary', '229 100% 64%')) }};
        --color-secondary: {{ str_replace(',', '', theme('dark-secondary', '237 33% 60%')) }};

        /* Neutral Colors - Borders, Accents... (Dark) */
        --color-neutral: {{ str_replace(',', '', theme('dark-neutral', '220 25% 29%')) }};

        /* Text Colors (Dark) */
        --color-base: {{ str_replace(',', '', theme('dark-base', '100 100% 100%')) }};
        --color-muted: {{ str_replace(',', '', theme('dark-muted', '220 28% 25%')) }};
        --color-inverted: {{ str_replace(',', '', theme('dark-inverted', '220 14% 60%')) }};

        /* Background Colors (Dark) */
        --color-background: {{ str_replace(',', '', theme('dark-background', '221 39% 11%')) }};
        --color-background-secondary: {{ str_replace(',', '', theme('dark-background-secondary', '217 33% 16%')) }};
    }
</style>