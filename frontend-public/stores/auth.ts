/**
 * Auth Store (Composition API)
 *
 * Manages authentication state with Bearer token stored in a cookie.
 * Uses useCookie('auth_token') for SSR-safe token persistence.
 *
 * Call `initialize()` to validate the token on app startup.
 * Middleware should `await initialize()` before checking auth state.
 */
import { defineStore } from 'pinia'
import { authService } from '~~/services'
import type { ApiResponse } from '~~/services/api/types'
import type { LoginCredentials, LoginResponse, RegisterData, User } from '~~/types'
import { useCartStore } from './cart'

/** Deduplication promise — ensures initialize() only runs once. */
let _initPromise: Promise<void> | null = null

export const useAuthStore = defineStore('auth', () => {
  // ── State ──────────────────────────────────────────────
  const user = ref<User | null>(null)
  const isLoading = ref(true)
  const tokenCookie = useCookie('auth_token', {
    maxAge: 60 * 60 * 24 * 30, // 30 days
    path: '/',
    sameSite: 'lax' as const,
  })

  // ── Getters ────────────────────────────────────────────
  const isAuthenticated = computed(() => !!user.value)
  const isEmailVerified = computed(() => !!user.value?.email_verified_at)
  const userName = computed(() => user.value?.name ?? 'Guest')

  // ── Internal helpers ───────────────────────────────────
  function setAuth(userData: User, token: string) {
    user.value = userData
    tokenCookie.value = token
    isLoading.value = false
  }

  function clearAuth() {
    user.value = null
    tokenCookie.value = null
    isLoading.value = false
    _initPromise = null
  }

  // ── Actions ────────────────────────────────────────────

  /**
   * Validate existing token by fetching user profile.
   * Safe to call multiple times — only the first call performs a network request.
   */
  function initialize(): Promise<void> {
    if (_initPromise) return _initPromise

    _initPromise = (async () => {
      if (tokenCookie.value) {
        try {
          user.value = await authService.me()
        } catch {
          tokenCookie.value = null
          user.value = null
        }
      } else {
        user.value = null
      }
      isLoading.value = false
    })()

    return _initPromise
  }

  async function login(credentials: LoginCredentials): Promise<ApiResponse<LoginResponse>> {
    const response = await authService.login(credentials)
    setAuth(response.data.user, response.data.token)
    return response
  }

  async function register(data: RegisterData): Promise<ApiResponse<LoginResponse>> {
    const response = await authService.register(data)
    setAuth(response.data.user, response.data.token)
    return response
  }

  async function logout() {
    try {
      await authService.logout()
    } finally {
      clearAuth()
      // Reset local cart state — server-side cart persists for when the user logs back in
      const cartStore = useCartStore()
      cartStore.resetLocal()
    }
  }

  async function fetchUser() {
    try {
      user.value = await authService.me()
    } catch {
      clearAuth()
    }
  }

  /**
   * Update local user state with partial data.
   * Used after profile update to avoid re-fetching.
   */
  function updateUser(userData: Partial<User>) {
    if (user.value) {
      user.value = { ...user.value, ...userData }
    }
  }

  return {
    // State
    user,
    isLoading,
    // Getters
    isAuthenticated,
    isEmailVerified,
    userName,
    // Actions
    initialize,
    login,
    register,
    logout,
    fetchUser,
    updateUser,
    clearAuth,
  }
}, {
  persist: {
    pick: ['user'],
  },
})
