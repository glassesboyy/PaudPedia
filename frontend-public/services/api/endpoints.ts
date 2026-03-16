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
    PROFILE: '/auth/profile',
    PROFILE_AVATAR: '/auth/profile/avatar',
  },

  // ── Courses ────────────────────────────────────────────
  COURSES: {
    LIST: '/courses',
    DETAIL: (slug: string) => `/courses/${slug}`,
    FEATURED: '/courses/featured',
    ENROLL: (id: number) => `/courses/${id}/enroll`,
    PROGRESS: (id: number) => `/courses/${id}/progress`,
  },

  // ── Webinars ───────────────────────────────────────────
  WEBINARS: {
    LIST: '/webinars',
    DETAIL: (slug: string) => `/webinars/${slug}`,
    FEATURED: '/webinars/featured',
  },

  // ── Products ───────────────────────────────────────────
  PRODUCTS: {
    LIST: '/products',
    DETAIL: (slug: string) => `/products/${slug}`,
    FEATURED: '/products/featured',
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
    DETAIL: (id: number) => `/mentors/${id}`,
    FEATURED: '/mentors/featured',
  },

  // ── Cart ───────────────────────────────────────────────
  CART: {
    INDEX: '/user/cart',
    ADD_ITEM: '/user/cart/items',
    UPDATE_ITEM: '/user/cart/items',
    REMOVE_ITEM: '/user/cart/items',
    CLEAR: '/user/cart',
    VALIDATE_PROMO: '/user/cart/validate-promo',
  },

  // ── Checkout ──────────────────────────────────────────
  CHECKOUT: {
    CREATE: '/user/checkout',
  },

  // ── Orders ─────────────────────────────────────────────
  ORDERS: {
    LIST: '/orders',
    DETAIL: (id: number) => `/orders/${id}`,
    CREATE: '/orders',
  },

  // ── User Dashboard ─────────────────────────────────────
  USER: {
    COURSES: '/user/courses',
    PRODUCTS: '/user/products',
    PRODUCT_DOWNLOAD: (id: number) => `/user/products/${id}/download`,
    WEBINARS: '/user/webinars',
    CERTIFICATES: '/user/certificates',
    TRANSACTIONS: '/user/transactions',
    TRANSACTION_DETAIL: (id: number) => `/user/transactions/${id}`,
  },

  // ── Landing ────────────────────────────────────────────
  LANDING: {
    INDEX: '/landing',
    HERO: '/landing/hero',
    STATISTICS: '/landing/statistics',
  },

  // ── Settings ───────────────────────────────────────────
  SETTINGS: {
    INDEX: '/settings',
    SHOW: (key: string) => `/settings/${key}`,
  },

  // ── Contact ────────────────────────────────────────────
  CONTACT: {
    INDEX: '/contact',
  },

  // ── Testimonials ───────────────────────────────────────
  TESTIMONIALS: {
    LIST: '/testimonials',
    FEATURED: '/testimonials/featured',
  },

  // ── Categories ─────────────────────────────────────────
  CATEGORIES: {
    LIST: '/categories',
    ARTICLES: '/categories/articles',
    PRODUCTS: '/categories/products',
    COURSES: '/categories/courses',
  },
} as const
