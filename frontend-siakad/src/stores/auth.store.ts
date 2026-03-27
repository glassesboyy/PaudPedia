import { defineStore } from 'pinia'
import { authService } from '@/features/auth/services/auth.service'
import type { LoginPayload } from '@/features/auth/services/auth.service'
import type { User } from '@/types'
import { useSchoolStore } from './school.store'

interface AuthState {
  user: User | null
  token: string | null
  isLoading: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isLoading: true,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    userName: (state) => state.user?.name ?? 'User',
  },

  actions: {
    /**
     * Set authentication data (from login or token callback).
     */
    async setAuth(token: string, userData?: User) {
      this.token = token
      if (userData) {
        this.user = userData
      } else {
        await this.fetchUser()
      }
      
      if (this.user) {
        const schoolStore = useSchoolStore()
        
        // Populate school memberships from user data if available to save a request
        if (this.user.school_memberships) {
          schoolStore.memberships = this.user.school_memberships
        } else {
          await schoolStore.fetchMemberships()
        }
      }
    },

    /**
     * Login — authenticate and fetch school memberships.
     */
    async login(payload: LoginPayload) {
      const response = await authService.login(payload)
      await this.setAuth(response.data.token, response.data.user)
    },

    /**
     * Fetch current user from /me endpoint.
     * Used to verify token on app init.
     */
    async fetchUser() {
      try {
        const response = await authService.fetchMe()
        this.user = response.data.user
      } catch {
        // Token invalid — clear auth
        this.token = null
        this.user = null
      }
    },

    /**
     * Initialize auth on app load.
     * Checks persisted token and validates it.
     */
    async initAuth() {
      if (!this.token) {
        this.isLoading = false
        return
      }

      try {
        await this.fetchUser()
        if (this.user) {
          await useSchoolStore().fetchMemberships()
        }
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Logout — clear all state and call API.
     */
    async logout() {
      try {
        if (this.token) {
          await authService.logout()
        }
      } catch {
        // Ignore errors during logout
      } finally {
        this.user = null
        this.token = null
        useSchoolStore().clearSchool()
      }
    },
  },

  persist: {
    pick: ['token', 'user'],
  },
})
