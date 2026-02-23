/**
 * HTTP Client Configuration
 *
 * Provides a pre-configured `$fetch` instance with:
 * - Base URL from runtime config
 * - Sanctum CSRF cookie handling (SSR)
 * - Global error interceptors (401, 422)
 */
import type { FetchOptions } from 'ofetch'

let _apiFetch: typeof $fetch | null = null

/**
 * Returns the singleton API fetch instance.
 * Must be called inside Nuxt context (plugin, composable, page, middleware).
 */
export function useApiFetch() {
  if (_apiFetch) return _apiFetch

  const config = useRuntimeConfig()

  _apiFetch = $fetch.create({
    baseURL: config.public.apiBase as string,
    credentials: 'include', // Sanctum cookie-based auth (SSR)

    onRequest({ options }) {
      // Attach CSRF token for state-changing requests
      const csrfToken = useCookie('XSRF-TOKEN')
      if (csrfToken.value) {
        const headers = new Headers(options.headers as HeadersInit)
        headers.set('X-XSRF-TOKEN', csrfToken.value)
        options.headers = headers
      }
    },

    onResponseError({ response }) {
      // 401 → session expired, force logout
      if (response.status === 401) {
        // Will be handled by auth store once implemented
        navigateTo('/auth/login')
      }
    },
  } as FetchOptions)

  return _apiFetch
}
