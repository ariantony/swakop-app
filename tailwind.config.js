const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
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
                primary: '#dc1d22',
            },
            spacing: {
                '1cm': '37.8px',
                '2cm': '75.6px',
                '3cm': '113.4px',
                '4cm': '151.2px',
                '5cm': '189.0px',
                '6cm': '226.8px',
                '7cm': '264.6px',
                '8cm': '302.4px',
                '9cm': '340.2px',
                '10cm': '378.0px',
            }
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
