/**
 * Auth Service
 *
 * Handles authentication-related API calls.
 */
import type { LoginCredentials, RegisterData, User } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const authService = {
  async csrfCookie(): Promise<void> {
    const config = useRuntimeConfig()
    await $fetch(API_ENDPOINTS.AUTH.CSRF_COOKIE, {
      baseURL: (config.public.apiBase as string).replace('/api/v1', ''),
      credentials: 'include',
    })
  },

  async login(credentials: LoginCredentials): Promise<ApiResponse<{ user: User; token: string }>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.LOGIN, {
      method: 'POST',
      body: credentials,
    })
  },

  async register(data: RegisterData): Promise<ApiResponse<{ user: User }>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.REGISTER, {
      method: 'POST',
      body: data,
    })
  },

  async logout(): Promise<void> {
    const apiFetch = useApiFetch()
    await apiFetch(API_ENDPOINTS.AUTH.LOGOUT, { method: 'POST' })
  },

  async me(): Promise<User> {
    const apiFetch = useApiFetch()
    const response = await apiFetch<ApiResponse<User>>(API_ENDPOINTS.AUTH.ME)
    return response.data
  },

  async forgotPassword(email: string): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.FORGOT_PASSWORD, {
      method: 'POST',
      body: { email },
    })
  },

  async resetPassword(data: { token: string; email: string; password: string; password_confirmation: string }): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.RESET_PASSWORD, {
      method: 'POST',
      body: data,
    })
  },
}
