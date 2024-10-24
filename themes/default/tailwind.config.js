const defaultTheme = require("tailwindcss/defaultTheme");
import path from 'path';
import autoprefixer from 'autoprefixer';
import tailwindcsstypography from '@tailwindcss/typography';
import tailwindcssforms from '@tailwindcss/forms';

module.exports = {
    content: [
        path.resolve(__dirname, "./**/*.{blade.php,js,vue,ts}"),
        './vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php',
        './extensions/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Nunito", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': {
                    100: 'var(--primary-100, #F3F4F6)',
                    200: 'var(--primary-200, #E5E7EB)',
                    300: 'var(--primary-300, #D1D5DB)',
                    400: 'var(--primary-400, #9CA3AF)',
                    500: 'var(--primary-500, #6B7280)',
                    600: 'var(--primary-600, #4B5563)',
                    700: 'var(--primary-700, #374151)',
                    800: 'var(--primary-800, #1F2937)',
                    900: 'var(--primary-900, #111827)',
                },
                'secondary': 'var(--secondary, #5270FD)',
            }
        },
    },

    variants: {
        extend: {
            opacity: ["disabled"],
        },
    },

    plugins: [
        tailwindcsstypography,
        autoprefixer,
        tailwindcssforms({
            strategy: 'class',
        })
    ],
};
