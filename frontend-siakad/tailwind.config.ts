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

        /* — Brand Scale (Enterprise Blue) —————————————————— */
        primary: {
          50: '#f0f7ff',
          100: '#e0effe',
          200: '#bae0fd',
          300: '#7cc2fb',
          400: '#38a1f7',
          500: '#0e82e5',
          600: '#0265c0',
          700: '#03509b',
          800: '#074480',
          900: '#0c3a6b',
          950: '#082547',
        },

        /* — Feedback Scales (Production Standard) —————————————— */
        success: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
          950: '#052e16',
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
          50: '#fef2f2',
          100: '#fee2e2',
          200: '#fecaca',
          300: '#fca5a5',
          400: '#f87171',
          500: '#ef4444',
          600: '#dc2626',
          700: '#b91c1c',
          800: '#991b1b',
          900: '#7f1d1d',
          950: '#450a0a',
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
