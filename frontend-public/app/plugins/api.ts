/**
 * API Plugin
 *
 * Initialises the API client and makes `useApiFetch` available globally.
 * The client is created lazily on first use inside services.
 */
export default defineNuxtPlugin(() => {
  // The API client initialises itself lazily via useApiFetch().
  // This plugin exists as a hook point for future enhancements
  // (e.g., attaching Sentry breadcrumbs, request logging).
})
