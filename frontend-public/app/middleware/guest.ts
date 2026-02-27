/**
 * Guest Middleware
 *
 * Redirects already-authenticated users away from guest-only pages
 * (e.g., login, register).
 */
import { useAuthStore } from '~~/stores/auth'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  if (authStore.isLoading) {
    await authStore.initialize()
  }

  if (authStore.isAuthenticated) {
    return navigateTo('/account')
  }
})
