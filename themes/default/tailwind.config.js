const defaultTheme = require("tailwindcss/defaultTheme");
import path from 'path';
import autoprefixer from 'autoprefixer';
import tailwindcsstypography from '@tailwindcss/typography';
import tailwindcssforms from '@tailwindcss/forms';

module.exports = {
    content: [
        path.resolve(__dirname, "./**/*.{blade.php,js,vue,ts}"),
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Nunito", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'test': '#111827',

                'primary': {
                    '100': '#F3F4F6',
                    '200': '#E5E7EB',
                    '300': '#D1D5DB',
                    '400': '#9CA3AF',
                    '500': '#6B7280',
                    '600': '#4B5563',
                    '700': '#374151',
                    '800': '#1F2937',
                    '900': '#111827',
                },
                'secondary': '#5270FD',

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
