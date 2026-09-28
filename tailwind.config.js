import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './config/travel.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                script: ['"Kaushan Script"', 'cursive'],
            },
            colors: {
                // Royal blue (#0047AB) is brand-700; every existing `brand-*` class
                // across the site picks up the Ram Tours palette automatically.
                brand: {
                    50: '#eef5ff',
                    100: '#d9e8ff',
                    200: '#bcd6ff',
                    300: '#8ebcff',
                    400: '#5996fd',
                    500: '#3370f8',
                    600: '#1d53ed',
                    700: '#0047ab',
                    800: '#0b3a8a',
                    900: '#0f3270',
                    950: '#0b1f47',
                },
                sky: {
                    DEFAULT: '#00aeef',
                    50: '#effaff',
                    100: '#def3ff',
                    200: '#b6eaff',
                    300: '#75dbff',
                    400: '#2cc9ff',
                    500: '#00aeef',
                    600: '#008fd4',
                    700: '#0072ab',
                    800: '#00608d',
                    900: '#065074',
                },
                gold: {
                    DEFAULT: '#f4b400',
                    50: '#fffbeb',
                    100: '#fff3c6',
                    200: '#ffe588',
                    300: '#ffd24a',
                    400: '#ffc020',
                    500: '#f4b400',
                    600: '#d98b00',
                    700: '#b46304',
                    800: '#924c0a',
                    900: '#783f0b',
                },
            },
            boxShadow: {
                card: '0 10px 30px -12px rgba(11, 31, 71, 0.18)',
                glow: '0 20px 45px -15px rgba(0, 71, 171, 0.45)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-14px)' },
                },
                marquee: {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
            },
            animation: {
                'fade-up': 'fade-up .7s ease-out both',
                float: 'float 6s ease-in-out infinite',
            },
        },
    },

    plugins: [forms, typography],
};
