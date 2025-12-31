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
            // Fuentes del proyecto
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
                mono: ['Space Grotesk', ...defaultTheme.fontFamily.mono],
            },

            // Custom colors del proyecto (extraídas de CSS variables actuales)
            colors: {
                'bg-main': '#05060A',
                'bg-card': '#0B0C12',
                'bg-sidebar': '#050814',
                'border-subtle': '#111827',
                'text-main': '#F9FAFB',
                'text-muted': '#9CA3AF',
                'accent-primary': '#FF3B5C',
                'accent-secondary': '#2DE38E',
                'accent-pink': '#FF4FA3',
            },

            // Breakpoints mobile-first (xs agregado para pantallas muy pequeñas)
            screens: {
                'xs': '475px',
                'sm': '640px',
                'md': '768px',
                'lg': '1024px',
                'xl': '1280px',
                '2xl': '1536px',
            },

            // Spacing custom para casos específicos
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },

            // Border radius custom
            borderRadius: {
                'card': '0.9rem',
                'btn': '0.7rem',
            },

            // Min touch target (WCAG compliance)
            minHeight: {
                'touch': '44px',
            },
            minWidth: {
                'touch': '44px',
            },
        },
    },

    plugins: [forms],
};
