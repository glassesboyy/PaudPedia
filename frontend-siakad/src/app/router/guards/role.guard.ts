import type { NavigationGuardWithThis, RouteLocationNormalized } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'

/**
 * Role-based access guard.
 *
 * Routes with `meta.roles[]`: only allow users whose current school role
 * is included in the allowed list.
 */
export const roleGuard: NavigationGuardWithThis<undefined> = (
  to: RouteLocationNormalized,
) => {
  const schoolStore = useSchoolStore()

  // Find the deepest matched route with roles meta
  const roleRoute = [...to.matched].reverse().find((r) => r.meta.roles)

  if (roleRoute?.meta.roles) {
    const allowedRoles = roleRoute.meta.roles as string[]
    const currentRole = schoolStore.currentRole

    if (!currentRole || !allowedRoles.includes(currentRole)) {
      // Redirect to dashboard — user doesn't have the right role
      return { name: 'Dashboard' }
    }
  }

  return true
}
