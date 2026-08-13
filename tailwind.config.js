import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },

            colors: {
                background: 'rgb(var(--color-background) / <alpha-value>)',
                surface: 'rgb(var(--color-surface) / <alpha-value>)',
                'surface-alt': 'rgb(var(--color-surface-alt) / <alpha-value>)',
                content: 'rgb(var(--color-content) / <alpha-value>)',
                'content-soft': 'rgb(var(--color-content-soft) / <alpha-value>)',
                muted: 'rgb(var(--color-muted) / <alpha-value>)',
                line: 'rgb(var(--color-line) / <alpha-value>)',
                'line-strong': 'rgb(var(--color-line-strong) / <alpha-value>)',

                primary: {
                    DEFAULT: 'rgb(var(--color-primary) / <alpha-value>)',
                    hover: 'rgb(var(--color-primary-hover) / <alpha-value>)',
                    soft: 'rgb(var(--color-primary-soft) / <alpha-value>)',
                    ring: 'rgb(var(--color-primary-ring) / <alpha-value>)',
                    fg: 'rgb(var(--color-primary-fg) / <alpha-value>)',
                },

                secondary: {
                    DEFAULT: 'rgb(var(--color-secondary) / <alpha-value>)',
                    hover: 'rgb(var(--color-secondary-hover) / <alpha-value>)',
                    soft: 'rgb(var(--color-secondary-soft) / <alpha-value>)',
                    fg: 'rgb(var(--color-secondary-fg) / <alpha-value>)',
                },

                success: {
                    DEFAULT: 'rgb(var(--color-success) / <alpha-value>)',
                    soft: 'rgb(var(--color-success-soft) / <alpha-value>)',
                },

                warning: {
                    DEFAULT: 'rgb(var(--color-warning) / <alpha-value>)',
                    soft: 'rgb(var(--color-warning-soft) / <alpha-value>)',
                },

                danger: {
                    DEFAULT: 'rgb(var(--color-danger) / <alpha-value>)',
                    soft: 'rgb(var(--color-danger-soft) / <alpha-value>)',
                },

                info: {
                    DEFAULT: 'rgb(var(--color-info) / <alpha-value>)',
                    soft: 'rgb(var(--color-info-soft) / <alpha-value>)',
                },
            },

            container: {
                center: true,
                padding: {
                    DEFAULT: '1rem',
                    sm: '1.5rem',
                    lg: '2rem',
                },
                screens: {
                    '2xl': '1280px',
                },
            },

            boxShadow: {
                soft: '0 1px 2px 0 rgb(0 0 0 / 0.04), 0 1px 3px 0 rgb(0 0 0 / 0.06)',
                card: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                lift: '0 12px 32px -12px rgb(0 0 0 / 0.18), 0 4px 12px -6px rgb(0 0 0 / 0.08)',
                glow: '0 0 0 4px rgb(var(--color-primary-ring) / 0.35)',
            },

            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },

            maxWidth: {
                prose: '65ch',
            },

            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(14px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },

            animation: {
                'fade-up': 'fade-up 0.6s ease-out both',
                'fade-in': 'fade-in 0.8s ease-out both',
                float: 'float 7s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
