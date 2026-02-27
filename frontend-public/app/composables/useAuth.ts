import { useAuthStore } from '~~/stores/auth'

/**
 * useAuth Composable
 *
 * Provides reactive auth state and helper methods.
 * Wraps the auth store for convenient usage in components.
 */
export function useAuth() {
  const authStore = useAuthStore()

  return {
    // Reactive state
    user: computed(() => authStore.user),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    isEmailVerified: computed(() => authStore.isEmailVerified),
    isLoading: computed(() => authStore.isLoading),
    userName: computed(() => authStore.userName),

    // Actions
    initialize: authStore.initialize,
    login: authStore.login,
    register: authStore.register,
    logout: authStore.logout,
    fetchUser: authStore.fetchUser,
  }
}
