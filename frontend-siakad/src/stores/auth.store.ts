import { defineStore } from 'pinia'
import type { User } from '@/types'

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
    // TODO: Implement login, registerSchool, fetchUser actions
    logout() {
      this.user = null
      this.token = null
    },
  },

  persist: {
    pick: ['token', 'user'],
  },
})
