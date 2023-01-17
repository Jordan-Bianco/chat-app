const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary': 'rgb(15, 14, 19)',
                'secondary': 'rgb(34, 36, 42)',
                'tertiary': 'rgba(48 49 56)'
            },
            animation: {
                'spin-fast': 'spin 0.5s linear infinite',
            }
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
