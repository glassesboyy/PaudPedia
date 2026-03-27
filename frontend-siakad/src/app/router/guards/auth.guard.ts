import type { NavigationGuardWithThis, RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'

/**
 * Auth navigation guard.
 *
 * - Routes with `meta.requiresAuth`: redirect unauthenticated users to /login
 * - Routes with `meta.guest`: redirect authenticated users to /select-school
 * - TokenCallback route: always accessible (handles cross-domain auth)
 */
export const authGuard: NavigationGuardWithThis<undefined> = async (
  to: RouteLocationNormalized,
) => {
  // Always allow token callback route
  if (to.name === 'TokenCallback') {
    return true
  }

  const authStore = useAuthStore()

  // Wait for initial auth check to complete
  if (authStore.isLoading) {
    await authStore.initAuth()
  }

  const isAuthenticated = authStore.isAuthenticated

  // Protected route — redirect to login
  if (to.matched.some((r) => r.meta.requiresAuth) && !isAuthenticated) {
    return {
      name: 'Login',
      query: { redirect: to.fullPath },
    }
  }

  // Guest-only route — redirect to school selector
  if (to.meta.guest && isAuthenticated) {
    return { name: 'SelectSchool' }
  }

  return true
}
