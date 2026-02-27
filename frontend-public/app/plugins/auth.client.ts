/**
 * Auth Initialization Plugin (client-only)
 *
 * Runs once on client-side app startup.
 * - If an auth_token cookie exists, validates it by calling /auth/me.
 * - Non-blocking: protected routes await initialization via middleware.
 */
import { useAuthStore } from '~~/stores/auth'

export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()
  // Fire and forget — middleware will await if needed
  authStore.initialize()
})
