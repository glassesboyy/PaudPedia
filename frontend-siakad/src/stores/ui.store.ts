import { defineStore } from 'pinia'

interface UiState {
  sidebarOpen: boolean
  theme: 'light' | 'dark'
}

export const useUiStore = defineStore('ui', {
  state: (): UiState => ({
    sidebarOpen: true,
    theme: 'light',
  }),

  actions: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen
    },
    setTheme(theme: 'light' | 'dark') {
      this.theme = theme
    },
  },

  persist: {
    pick: ['theme'],
  },
})
