/**
 * Auth Middleware
 *
 * Redirects unauthenticated users to the login page.
 * Waits for auth initialization to complete before checking.
 * Stores the intended destination in the `redirect` query param.
 */
import { useAuthStore } from '~~/stores/auth'

export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  if (authStore.isLoading) {
    await authStore.initialize()
  }

  if (!authStore.isAuthenticated) {
    return navigateTo({
      path: '/auth/login',
      query: { redirect: to.fullPath },
    })
  }
})
