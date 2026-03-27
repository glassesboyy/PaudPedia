import type { NavigationGuardWithThis, RouteLocationNormalized } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'

/**
 * School context guard.
 *
 * Routes with `meta.requiresSchool`: redirect to /select-school
 * if no school has been selected yet.
 */
export const schoolGuard: NavigationGuardWithThis<undefined> = (
  to: RouteLocationNormalized,
) => {
  const schoolStore = useSchoolStore()

  if (to.matched.some((r) => r.meta.requiresSchool) && !schoolStore.currentSchoolId) {
    return { name: 'SelectSchool' }
  }

  return true
}
