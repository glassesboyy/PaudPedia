import { defineStore } from 'pinia'
import { authService } from '@/features/auth/services/auth.service'
import { subscriptionService } from '@/features/school/services/subscription.service'
import type { School, SchoolMembership } from '@/types'
import type { SubscriptionInfo } from '@/types/subscription.types'

interface SchoolState {
  currentSchool: School | null
  memberships: SchoolMembership[]
  subscriptionInfo: SubscriptionInfo | null
  isLoading: boolean
}

export const useSchoolStore = defineStore('school', {
  state: (): SchoolState => ({
    currentSchool: null,
    memberships: [],
    subscriptionInfo: null,
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
    isCoreLocked(state): boolean {
      if (!state.subscriptionInfo) return false
      if (state.subscriptionInfo.is_pro) return false
      
      const s = state.subscriptionInfo
      const studentOver = s.student_limit !== null && s.student_usage > s.student_limit
      const teacherOver = s.teacher_limit !== null && s.teacher_usage > s.teacher_limit
      
      return studentOver || teacherOver
    },
    daysUntilExpiration(state): number | null {
      if (!state.subscriptionInfo?.subscription_ended_at) return null
      if (!state.subscriptionInfo.is_pro) return null
      
      const end = new Date(state.subscriptionInfo.subscription_ended_at).getTime()
      const now = new Date().getTime()
      const diff = end - now
      
      if (diff < 0) return 0
      return Math.ceil(diff / (1000 * 60 * 60 * 24))
    }
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
          const freshMembership = this.memberships.find(
            (m) => m.school_id === this.currentSchool?.id,
          )
          if (freshMembership) {
            this.currentSchool = freshMembership.school
          } else {
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
    async selectSchool(schoolId: number) {
      const membership = this.memberships.find((m) => m.school_id === schoolId)
      if (membership) {
        this.currentSchool = membership.school
        await this.fetchSubscriptionInfo()
      }
    },

    /**
     * Fetch subscription info for current school.
     */
    async fetchSubscriptionInfo() {
      if (!this.currentSchoolId) return
      try {
        const res = await subscriptionService.getInfo(this.currentSchoolId)
        this.subscriptionInfo = res as any
      } catch {
        this.subscriptionInfo = null
      }
    },

    /**
     * Clear current school context.
     */
    clearSchool() {
      this.currentSchool = null
      this.subscriptionInfo = null
      // memberships = [] // REMOVED: keep memberships for the selection screen
    },
  },

  persist: {
    pick: ['currentSchool'],
  },
})
