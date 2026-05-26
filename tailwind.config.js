import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-beige': '#f5f1ea',
                navy: {
                    DEFAULT: '#001d6d',
                    50: '#e8ecf8',
                    100: '#c5ceee',
                    200: '#9aaee3',
                    300: '#6f8dd8',
                    400: '#4f74d0',
                    500: '#3059c8',
                    600: '#2b51bf',
                    700: '#2447b4',
                    800: '#1d3da8',
                    900: '#122d91',
                    950: '#001d6d',
                },
                brand: {
                    DEFAULT: '#3964b0',
                    50: '#e8eef8',
                    100: '#c5d4ed',
                    200: '#9eb8e1',
                    300: '#779cd5',
                    400: '#5a86cb',
                    500: '#3964b0',  // primary brand blue
                    600: '#3358a8',
                    700: '#2b4a9f',
                    800: '#243c96',
                    900: '#162986',
                },
                amber: {
                    DEFAULT: '#bb732b',
                    50: '#fdf3e7',
                    100: '#fae1c3',
                    200: '#f7cd9b',
                    300: '#f4b973',
                    400: '#f1aa55',
                    500: '#ee9b37',
                    600: '#e98f31',
                    700: '#e2802a',
                    800: '#dc7123',
                    900: '#bb732b',
                },
                stone: {
                    DEFAULT: '#c4b9aa',
                    50: '#f7f5f2',
                    100: '#ece8e2',
                    200: '#ddd7ce',
                    300: '#cec5ba',
                    400: '#c4b9aa',
                    500: '#b5a796',
                    600: '#a49283',
                    700: '#8e7c6e',
                    800: '#756659',
                    900: '#5c4f45',
                },
            },
            boxShadow: {
                'card': '0 2px 8px 0 rgba(0, 29, 109, 0.08)',
                'card-hover': '0 4px 16px 0 rgba(0, 29, 109, 0.14)',
            },
        },
    },

    plugins: [forms],
};
