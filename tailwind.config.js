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
                primary: '#aeca7e',
                secondary: '#506b1c',
                neutral: {
                    900: '#1e1e1e',
                    500: '#6b6b6b',
                    100: '#f5f7f1',
                },
            },
            gradientColorStops: {
                primary: '#aeca7e',
                secondary: '#506b1c',
                light: '#f5f7f1',
                dark: '#1e1e1e',
            },
            backgroundImage: {
                'grad-primary': 'linear-gradient(135deg, #aeca7e 0%, #506b1c 100%)',
                'grad-subtle': 'linear-gradient(135deg, #f5f7f1 0%, #aeca7e 50%)',
                'grad-dark': 'linear-gradient(135deg, #1e1e1e 0%, #506b1c 100%)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
