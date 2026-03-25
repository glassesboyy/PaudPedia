import { defineStore } from 'pinia'
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
    // TODO: Implement fetchMemberships, selectSchool actions
    clearSchool() {
      this.currentSchool = null
      localStorage.removeItem('currentSchoolId')
    },
  },

  persist: {
    pick: ['currentSchool'],
  },
})
