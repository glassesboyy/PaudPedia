import { useUIStore } from "~~/stores/ui"

/**
 * useToast Composable
 *
 * Provides toast notification helpers backed by the UI store.
 */
export function useToast() {
  const uiStore = useUIStore()

  return {
    success: (message: string) => uiStore.addToast('success', message),
    error: (message: string) => uiStore.addToast('error', message),
    warning: (message: string) => uiStore.addToast('warning', message),
    info: (message: string) => uiStore.addToast('info', message),
    remove: (id: number) => uiStore.removeToast(id),
  }
}
