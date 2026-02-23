/**
 * User Preferences Store
 *
 * Persisted user preferences (theme, locale, etc.)
 */
import { defineStore } from 'pinia'

interface UserPreferencesState {
  locale: string
}

export const useUserStore = defineStore('user', {
  state: (): UserPreferencesState => ({
    locale: 'id',
  }),

  actions: {
    setLocale(locale: string) {
      this.locale = locale
    },
  },

  persist: true,
})
