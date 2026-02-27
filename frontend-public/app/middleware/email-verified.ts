/**
 * Email Verified Middleware
 *
 * Ensures the authenticated user has verified their email address.
 * Must be placed AFTER the auth middleware in route definitions.
 */
import { useAuthStore } from '~~/stores/auth'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  if (authStore.isLoading) {
    await authStore.initialize()
  }

  if (authStore.user && !authStore.isEmailVerified) {
    return navigateTo('/auth/verify-email')
  }
})
