import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', 'sans-serif']
            },
            colors: {
                brandblue: '#1D3652',
                brandgold: '#F0B42FE5'
            }
        },
    },

    plugins: [forms],
    darkMode: 'class',
};
