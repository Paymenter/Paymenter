const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
    darkMode: 'class',
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./themes/default/**/*.{blade.php,js,vue,ts}",
    ],

    theme: {
        // Colors
        extend: {
            colors: {
                white: "#636B77",
                accent: '#CFE2FD',
                normal: '#E7F0FE',
                button: '#5270fd',
                darkbutton: '#2f3949',
                darkmode: '#1A202C',
                darkmode2: '#252D3B',
            },
            fontFamily: {
                sans: ["Nunito", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    variants: {
        extend: {
            opacity: ["disabled"],
        },
    },

    plugins: [require("@tailwindcss/forms")],
};
