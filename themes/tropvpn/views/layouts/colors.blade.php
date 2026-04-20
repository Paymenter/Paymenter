<style>
    :root {
        /* Light Mode */
        --color-primary-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('primary', '229 80% 55%'))) }};
        --color-secondary-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('secondary', '255 60% 55%'))) }};
        --color-neutral-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('neutral', '235 20% 85%'))) }};
        --color-base-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('base', '235 15% 10%'))) }};
        --color-muted-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('muted', '235 10% 45%'))) }};
        --color-inverted-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('inverted', '0 0% 100%'))) }};
        --color-background-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('background', '0 0% 100%'))) }};
        --color-background-secondary-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('background-secondary', '235 20% 97%'))) }};
    }

    .dark {
        /* Dark Mode — TropVPN palette */
        --color-primary-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-primary', '229 80% 62%'))) }};
        --color-secondary-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-secondary', '255 60% 60%'))) }};
        --color-neutral-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-neutral', '235 20% 18%'))) }};
        --color-base-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-base', '0 0% 98%'))) }};
        --color-muted-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-muted', '235 10% 55%'))) }};
        --color-inverted-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-inverted', '0 0% 100%'))) }};
        --color-background-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-background', '235 25% 8%'))) }};
        --color-background-secondary-raw: {{ str_replace(',', '', preg_replace('/^hsl\((.+)\)$/', '$1', theme('dark-background-secondary', '235 20% 11%'))) }};
    }
</style>
