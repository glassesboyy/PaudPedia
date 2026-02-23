/**
 * UI Store
 *
 * Global UI state: modals, toasts, sidebar toggle, etc.
 */
import { defineStore } from 'pinia'

interface Toast {
  id: number
  type: 'success' | 'error' | 'warning' | 'info'
  message: string
}

interface UIState {
  isMobileSidebarOpen: boolean
  toasts: Toast[]
  _toastId: number
}

export const useUIStore = defineStore('ui', {
  state: (): UIState => ({
    isMobileSidebarOpen: false,
    toasts: [],
    _toastId: 0,
  }),

  actions: {
    toggleMobileSidebar() {
      this.isMobileSidebarOpen = !this.isMobileSidebarOpen
    },

    closeMobileSidebar() {
      this.isMobileSidebarOpen = false
    },

    addToast(type: Toast['type'], message: string, duration = 5000) {
      const id = ++this._toastId
      this.toasts.push({ id, type, message })

      if (duration > 0) {
        setTimeout(() => this.removeToast(id), duration)
      }
    },

    removeToast(id: number) {
      const index = this.toasts.findIndex((t) => t.id === id)
      if (index > -1) this.toasts.splice(index, 1)
    },
  },

  // UI state should NOT be persisted
})
