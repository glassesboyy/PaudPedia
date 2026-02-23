/**
 * Auth Store
 *
 * Manages authentication state: current user, loading flag, and auth actions.
 */
import { defineStore } from 'pinia'
import { authService } from '~~/services'
import type { LoginCredentials, RegisterData, User } from '~~/types'

interface AuthState {
  user: User | null
  isLoading: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    isLoading: true,
  }),

  getters: {
    isAuthenticated: (state): boolean => !!state.user,
    userName: (state): string => state.user?.name ?? 'Guest',
  },

  actions: {
    async fetchUser() {
      this.isLoading = true
      try {
        this.user = await authService.me()
      } catch {
        this.user = null
      } finally {
        this.isLoading = false
      }
    },

    async login(credentials: LoginCredentials) {
      await authService.csrfCookie()
      const response = await authService.login(credentials)
      this.user = response.data.user
      return response.data.user
    },

    async register(data: RegisterData) {
      await authService.csrfCookie()
      const response = await authService.register(data)
      this.user = response.data.user
      return response.data.user
    },

    async logout() {
      await authService.logout()
      this.user = null
      navigateTo('/auth/login')
    },
  },

  persist: {
    pick: ['user'],
  },
})
