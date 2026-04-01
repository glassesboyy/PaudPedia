import { defineStore } from 'pinia'
import { authService } from '@/features/auth/services/auth.service'
import type { School, SchoolMembership } from '@/types'

interface SchoolState {
  currentSchool: School | null
  memberships: SchoolMembership[]
  isLoading: boolean
}

export const useSchoolStore = defineStore('school', {
  state: (): SchoolState => ({
    currentSchool: null,
    memberships: [],
    isLoading: true,
  }),

  getters: {
    currentSchoolId: (state) => state.currentSchool?.id,
    currentRole: (state) => {
      if (!state.currentSchool) return null
      const membership = state.memberships.find(
        (m) => m.school_id === state.currentSchool?.id,
      )
      return membership?.role_type ?? null
    },
    isPro: (state) => state.currentSchool?.subscription_plan === 'pro',
    hasMultipleSchools: (state) => state.memberships.length > 1,
    isHeadmaster(): boolean {
      return this.currentRole === 'headmaster'
    },
    isTeacher(): boolean {
      return this.currentRole === 'teacher'
    },
    isParent(): boolean {
      return this.currentRole === 'parent'
    },
  },

  actions: {
    /**
     * Fetch all school memberships for the current user.
     */
    async fetchMemberships() {
      this.isLoading = true
      try {
        const response = await authService.fetchMemberships()
        this.memberships = response.data

        // Auto-restore last selected school if still valid
        if (this.currentSchool) {
          const stillValid = this.memberships.some(
            (m) => m.school_id === this.currentSchool?.id,
          )
          if (!stillValid) {
            this.currentSchool = null
          }
        }
      } catch {
        this.memberships = []
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Select a school to work with.
     */
    selectSchool(schoolId: number) {
      const membership = this.memberships.find((m) => m.school_id === schoolId)
      if (membership) {
        this.currentSchool = membership.school
      }
    },

    /**
     * Clear current school context.
     */
    clearSchool() {
      this.currentSchool = null
      // memberships = [] // REMOVED: keep memberships for the selection screen
    },
  },

  persist: {
    pick: ['currentSchool'],
  },
})
