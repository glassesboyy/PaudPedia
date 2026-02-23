/**
 * Auth Middleware
 *
 * Redirects unauthenticated users to the login page.
 * Stores the intended destination in the `redirect` query param.
 */
export default defineNuxtRouteMiddleware((to) => {
  const { isAuthenticated } = useAuth()

  if (!isAuthenticated.value) {
    return navigateTo({
      path: '/auth/login',
      query: { redirect: to.fullPath },
    })
  }
})
