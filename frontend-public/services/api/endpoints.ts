/**
 * API Endpoint Constants
 *
 * Single source of truth for all backend route paths.
 * Grouped by domain / feature.
 */

export const API_ENDPOINTS = {
  // ── Auth ───────────────────────────────────────────────
  AUTH: {
    LOGIN: '/auth/login',
    REGISTER: '/auth/register',
    LOGOUT: '/auth/logout',
    ME: '/auth/me',
    FORGOT_PASSWORD: '/auth/forgot-password',
    RESET_PASSWORD: '/auth/reset-password',
    VERIFY_EMAIL: (id: string, hash: string) => `/auth/email/verify/${id}/${hash}`,
    RESEND_VERIFICATION: '/auth/email/verification-notification',
    CHANGE_PASSWORD: '/auth/change-password',
    CSRF_COOKIE: '/sanctum/csrf-cookie',
  },

  // ── Courses ────────────────────────────────────────────
  COURSES: {
    LIST: '/courses',
    DETAIL: (slug: string) => `/courses/${slug}`,
    ENROLL: (id: number) => `/courses/${id}/enroll`,
    PROGRESS: (id: number) => `/courses/${id}/progress`,
  },

  // ── Webinars ───────────────────────────────────────────
  WEBINARS: {
    LIST: '/webinars',
    DETAIL: (slug: string) => `/webinars/${slug}`,
  },

  // ── Products ───────────────────────────────────────────
  PRODUCTS: {
    LIST: '/products',
    DETAIL: (slug: string) => `/products/${slug}`,
  },

  // ── Articles ───────────────────────────────────────────
  ARTICLES: {
    LIST: '/articles',
    DETAIL: (slug: string) => `/articles/${slug}`,
    FEATURED: '/articles/featured',
    RELATED: (slug: string) => `/articles/${slug}/related`,
  },

  // ── Mentors ────────────────────────────────────────────
  MENTORS: {
    LIST: '/mentors',
    DETAIL: (slug: string) => `/mentors/${slug}`,
  },

  // ── Cart ───────────────────────────────────────────────
  CART: {
    GET: '/cart',
    ADD: '/cart/items',
    REMOVE: (itemId: number) => `/cart/items/${itemId}`,
    VALIDATE_PROMO: '/cart/validate-promo',
  },

  // ── Orders ─────────────────────────────────────────────
  ORDERS: {
    LIST: '/orders',
    DETAIL: (id: number) => `/orders/${id}`,
    CREATE: '/orders',
  },

  // ── Categories ─────────────────────────────────────────
  CATEGORIES: {
    LIST: '/categories',
  },
} as const
