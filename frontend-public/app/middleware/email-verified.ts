/**
 * Email Verified Middleware
 *
 * Ensures the authenticated user has verified their email address.
 */
export default defineNuxtRouteMiddleware(() => {
  const { user } = useAuth()

  if (user.value && !user.value.email_verified_at) {
    return navigateTo('/auth/verify-email')
  }
})
