/**
 * Guest Middleware
 *
 * Redirects already-authenticated users away from guest-only pages
 * (e.g., login, register).
 */
export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated } = useAuth()

  if (isAuthenticated.value) {
    return navigateTo('/account')
  }
})
