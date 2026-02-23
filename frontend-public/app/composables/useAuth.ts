import { useAuthStore } from "~~/stores/auth"

/**
 * useAuth Composable
 *
 * Provides reactive auth state and helper methods.
 * Wraps the auth store for convenient usage in components.
 */
export function useAuth() {
  const authStore = useAuthStore()

  const isAuthenticated = computed(() => authStore.isAuthenticated)
  const user = computed(() => authStore.user)
  const isLoading = computed(() => authStore.isLoading)

  return {
    user,
    isAuthenticated,
    isLoading,
    login: authStore.login,
    register: authStore.register,
    logout: authStore.logout,
    fetchUser: authStore.fetchUser,
  }
}
