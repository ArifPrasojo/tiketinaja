import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'pitch-black': '#0F172A',
                'electric-blue': '#0052FF',
                'athletic-orange': '#FF5C00',
                'field-green': '#22C55E',
                'stadium-gray': '#E2E8F0',
                'surface-light': '#faf9fa',
                'on-surface-variant': '#434653',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Noto Serif', 'serif'],
            },
        },
    },

    plugins: [forms],
};
