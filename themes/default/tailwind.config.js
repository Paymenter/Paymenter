const defaultTheme = require('tailwindcss/defaultTheme')
import path from 'path'
import autoprefixer from 'autoprefixer'
import tailwindcsstypography from '@tailwindcss/typography'
import tailwindcssforms from '@tailwindcss/forms'

module.exports = {
    darkMode: 'class',
    content: [
        path.resolve(__dirname, './**/*.{blade.php,js,vue,ts}'),
        './extensions/**/*.blade.php'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans]
            },
            colors: {
                // Branding Colors
                primary: 'hsl(var(--color-primary) / <alpha-value>)',
                secondary: 'hsl(var(--color-secondary) / <alpha-value>)',

                // Neutral Colors
                neutral: 'hsl(var(--color-neutral) / <alpha-value>)',

                // Text Colors
                base: 'hsl(var(--color-base) / <alpha-value>)',
                muted: 'hsl(var(--color-muted) / <alpha-value>)',
                inverted: 'hsl(var(--color-inverted) / <alpha-value>)',

                // State Colors
                success: 'hsl(var(--color-success) / <alpha-value>)',
                error: 'hsl(var(--color-error) / <alpha-value>)',
                warning: 'hsl(var(--color-warning) / <alpha-value>)',
                inactive: 'hsl(var(--color-inactive) / <alpha-value>)',
                info: 'hsl(var(--color-info) / <alpha-value>)',

                // Background Colors
                background: {
                    DEFAULT: 'hsl(var(--color-background) / <alpha-value>)',
                    secondary: 'hsl(var(--color-background-secondary) / <alpha-value>)',
                },
            },
        },
    },

    variants: {
        extend: {
            opacity: ['disabled']
        }
    },

    plugins: [
        tailwindcsstypography,
        autoprefixer,
        tailwindcssforms({
            strategy: 'class'
        })
    ]
}
