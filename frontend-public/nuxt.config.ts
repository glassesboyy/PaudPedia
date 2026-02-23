// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',

  devtools: { enabled: true },

  // ── Modules ──────────────────────────────────────────────
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    'pinia-plugin-persistedstate/nuxt',
    '@nuxt/image',
    '@nuxt/icon',
    '@vueuse/nuxt',
  ],

  // ── Runtime Config ───────────────────────────────────────
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api/v1',
      appName: process.env.NUXT_PUBLIC_APP_NAME || 'PaudPedia',
      appUrl: process.env.NUXT_PUBLIC_APP_URL || 'http://localhost:3000',
    },
  },

  // ── App Head Defaults ────────────────────────────────────
  app: {
    head: {
      charset: 'utf-8',
      viewport: 'width=device-width, initial-scale=1',
      title: 'PaudPedia - Platform E-Learning PAUD',
      meta: [
        { name: 'description', content: 'Platform e-learning dan marketplace untuk pendidikan anak usia dini (PAUD)' },
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      ],
    },
  },

  // ── Route Rules (Hybrid Rendering) ──────────────────────
  routeRules: {
    // SSG for static pages
    '/': { prerender: true },

    // ISR for articles (stale-while-revalidate 1 hour)
    '/articles': { swr: 3600 },
    '/articles/**': { swr: 3600 },

    // SSR for dynamic catalog pages
    '/courses': { ssr: true },
    '/courses/**': { ssr: true },
    '/webinars': { ssr: true },
    '/webinars/**': { ssr: true },
    '/products': { ssr: true },
    '/products/**': { ssr: true },
    '/mentors': { ssr: true },
    '/mentors/**': { ssr: true },

    // CSR for user-specific pages
    '/account/**': { ssr: false },
    '/learn/**': { ssr: false },
    '/checkout': { ssr: false },
    '/cart': { ssr: false },
  },

  // ── Tailwind CSS ─────────────────────────────────────────
  tailwindcss: {
    cssPath: '~/assets/css/main.css',
  },

  // ── Pinia ────────────────────────────────────────────────
  pinia: {
    storesDirs: ['./stores/**'],
  },

  // ── Image ────────────────────────────────────────────────
  image: {
    quality: 80,
    format: ['webp', 'avif'],
  },

  // ── TypeScript ───────────────────────────────────────────
  typescript: {
    strict: true,
    typeCheck: false, // Enable in CI for faster dev
    tsConfig: {
      include: [
        '../services/**/*',
        '../stores/**/*',
        '../types/**/*',
      ],
    },
  },
})
