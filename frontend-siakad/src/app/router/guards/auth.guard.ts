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

  // Protected route — redirect to login or verification
  if (to.matched.some((r) => r.meta.requiresAuth)) {
    if (!isAuthenticated) {
      return {
        name: 'Login',
        query: { redirect: to.fullPath },
      }
    }

    // User is authenticated, but check if email is verified
    if (authStore.user && !authStore.user.email_verified_at) {
      const publicUrl = import.meta.env.VITE_PUBLIC_URL || 'http://localhost:3000'
      window.location.href = `${publicUrl}/auth/verify-email`
      return false
    }
  }

  // Guest-only route — redirect to school selector
  if (to.meta.guest && isAuthenticated) {
    if (authStore.user && !authStore.user.email_verified_at) {
        // Unverified users should not see guess routes either if they are 'logged in' but restricted
        const publicUrl = import.meta.env.VITE_PUBLIC_URL || 'http://localhost:3000'
        window.location.href = `${publicUrl}/auth/verify-email`
        return false
    }
    return { name: 'SelectSchool' }
  }

  return true
}
