/**
 * HTTP Client Configuration
 *
 * Provides a pre-configured `$fetch` instance with:
 * - Base URL from runtime config
 * - Bearer token auth via auth_token cookie
 * - Global error interceptors (401)
 *
 * The factory is SSR-safe: a fresh instance is created on every server
 * request to avoid cross-request data leakage while the client reuses
 * a singleton for the entire SPA session.
 */
import type { FetchOptions } from 'ofetch'

let _apiFetch: typeof $fetch | null = null

/**
 * Returns the API fetch instance.
 * Must be called inside Nuxt context (plugin, composable, page, middleware).
 */
export function useApiFetch() {
  // On the server, always create a fresh instance (no cross-request leaks).
  // On the client, reuse the singleton.
  if (import.meta.server || !_apiFetch) {
    const config = useRuntimeConfig()

    _apiFetch = $fetch.create({
      baseURL: config.public.apiBase as string,

      onRequest({ options }) {
        // Read cookie inside the hook so we always get the latest value.
        // Capturing useCookie outside would create a separate ref from the
        // auth store (different options → different Vue ref in Nuxt 3).
        const token = useCookie('auth_token')
        const headers = new Headers(options.headers as HeadersInit)
        headers.set('Accept', 'application/json')

        if (token.value) {
          headers.set('Authorization', `Bearer ${token.value}`)
        }

        options.headers = headers
      },

      onResponseError({ response }) {
        // 401 → token expired / invalid — clear it silently.
        // Auth middleware handles the redirect to login.
        if (response.status === 401) {
          const token = useCookie('auth_token')
          token.value = null
        }
      },
    } as FetchOptions)
  }

  return _apiFetch!
}
