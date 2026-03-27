import type { Config } from 'tailwindcss'

/**
 * PaudPedia SIAKAD Design System — Tailwind Configuration
 *
 * Consistent with frontend-public design system.
 * Uses Inter font (professional) instead of Comic Relief (playful).
 *
 * Architecture:
 *   CSS custom properties (variables.css :root) → Tailwind semantic tokens (this file)
 *
 * Color conventions:
 *   Brand scales:     text-primary-600, bg-secondary-500, ...
 *   Neutral semantic: bg-background, bg-surface, text-foreground, text-heading, ...
 *   Feedback scales:  text-success-600, bg-warning-100, text-danger-600, ...
 */

/** Helper: reference a CSS custom property as an rgb() Tailwind color with opacity support */
const withOpacity = (varName: string) =>
  `rgb(var(${varName}) / <alpha-value>)` as unknown as string

export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],

  theme: {
    /* ── Container ──────────────────────────────────────────── */
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
        sm: '1.5rem',
        lg: '2rem',
      },
      screens: {
        sm: '640px',
        md: '768px',
        lg: '1024px',
        xl: '1280px',
        '2xl': '1280px',
      },
    },

    /* ── Theme Extensions ───────────────────────────────────── */
    extend: {
      /* ── Semantic Colors ────────────────────────────────────── */
      colors: {
        /* — Neutral Semantic Tokens (CSS-var based) —————————— */
        background:     withOpacity('--color-background'),
        surface: {
          DEFAULT:      withOpacity('--color-surface'),
          muted:        withOpacity('--color-surface-muted'),
          sunken:       withOpacity('--color-surface-sunken'),
        },
        foreground:     withOpacity('--color-foreground'),
        heading:        withOpacity('--color-heading'),
        body:           withOpacity('--color-body'),
        muted:          withOpacity('--color-muted'),
        'on-primary':   withOpacity('--color-on-primary'),
        border: {
          DEFAULT:      withOpacity('--color-border'),
          muted:        withOpacity('--color-border-muted'),
        },
        ring:           withOpacity('--color-ring'),

        /* — Brand Scale (identical to frontend-public) ————————— */
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
          950: '#172554',
        },
        secondary: {
          50: '#fff8ed',
          100: '#ffefcf',
          200: '#ffdc9e',
          300: '#ffc262',
          400: '#ffa42b',
          500: '#ff8c07',
          600: '#ef6f00',
          700: '#c85200',
          800: '#9f400b',
          900: '#833610',
          950: '#461905',
        },

        /* — Feedback Scales ——————————————————————————————————— */
        success: {
          50: '#ecfdf5',
          100: '#d1fae5',
          200: '#a7f3d0',
          300: '#6ee7b7',
          400: '#34d399',
          500: '#10b981',
          600: '#059669',
          700: '#047857',
          800: '#065f46',
          900: '#064e3b',
          950: '#022c22',
        },
        warning: {
          50: '#fffbeb',
          100: '#fef3c7',
          200: '#fde68a',
          300: '#fcd34d',
          400: '#fbbf24',
          500: '#f59e0b',
          600: '#d97706',
          700: '#b45309',
          800: '#92400e',
          900: '#78350f',
          950: '#451a03',
        },
        danger: {
          50: '#fff1f2',
          100: '#ffe4e6',
          200: '#fecdd3',
          300: '#fda4af',
          400: '#fb7185',
          500: '#f43f5e',
          600: '#e11d48',
          700: '#be123c',
          800: '#9f1239',
          900: '#881337',
          950: '#4c0519',
        },
      },

      /* ── Font Family ────────────────────────────────────────── */
      fontFamily: {
        sans: ['"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
      },

      /* ── Border Radius ──────────────────────────────────────── */
      borderRadius: {
        '4xl': '2rem',
        '5xl': '2.5rem',
      },

      /* ── Box Shadow ─────────────────────────────────────────── */
      boxShadow: {
        soft: '0 2px 8px rgba(0, 0, 0, 0.04)',
        card: '0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04)',
        medium: '0 8px 24px rgba(0, 0, 0, 0.08)',
        strong: '0 12px 36px rgba(0, 0, 0, 0.12)',
        elevated: '0 20px 60px rgba(0, 0, 0, 0.1), 0 8px 20px rgba(0, 0, 0, 0.06)',
      },

      /* ── Animation ──────────────────────────────────────────── */
      animation: {
        'fade-in': 'fadeIn 0.3s ease-out',
        'fade-in-up': 'fadeInUp 0.4s ease-out',
        'slide-down': 'slideDown 0.2s ease-out',
        'scale-in': 'scaleIn 0.2s ease-out',
        'spin-slow': 'spin 3s linear infinite',
      },
      keyframes: {
        fadeIn: {
          from: { opacity: '0' },
          to: { opacity: '1' },
        },
        fadeInUp: {
          from: { opacity: '0', transform: 'translateY(8px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        slideDown: {
          from: { opacity: '0', transform: 'translateY(-4px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        scaleIn: {
          from: { opacity: '0', transform: 'scale(0.95)' },
          to: { opacity: '1', transform: 'scale(1)' },
        },
      },

      /* ── Transition Timing ──────────────────────────────────── */
      transitionTimingFunction: {
        smooth: 'cubic-bezier(0.4, 0, 0.2, 1)',
        bounce: 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
      },

      /* ── Z-Index Scale ──────────────────────────────────────── */
      zIndex: {
        dropdown: '1000',
        sticky: '1020',
        fixed: '1030',
        overlay: '1040',
        modal: '1050',
        popover: '1060',
        tooltip: '1070',
      },
    },
  },

  plugins: [],
} satisfies Config
