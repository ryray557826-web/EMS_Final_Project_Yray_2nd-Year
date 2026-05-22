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
                brand: {
                    DEFAULT: '#0a0a0a', // Background
                    card: '#161616',    // Card
                    pink: '#ff2d75',    // Accents
                }
            }
        },
    },
    plugins: [require('@tailwindcss/forms')],
};