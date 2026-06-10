/**
 * Email Verified Middleware
 *
 * Ensures the authenticated user has verified their email address.
 * Must be placed AFTER the auth middleware in route definitions.
 */
import { useAuthStore } from '~~/stores/auth'

export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  if (authStore.isLoading) {
    await authStore.initialize()
  }

  // Lock unverified authenticated users to the verify-email page
  if (authStore.isAuthenticated && !authStore.isEmailVerified) {
    if (!to.path.startsWith('/auth/verify-email')) {
      return navigateTo('/auth/verify-email')
    }
  }
})
