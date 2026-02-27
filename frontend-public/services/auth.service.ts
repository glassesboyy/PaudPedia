/**
 * Auth Service
 *
 * Handles authentication-related API calls.
 * Uses Bearer token auth stored in auth_token cookie.
 */
import type {
    ChangePasswordData,
    LoginCredentials,
    LoginResponse,
    RegisterData,
    ResetPasswordData,
    User,
} from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const authService = {
  // ── Core Auth ────────────────────────────────────────────

  async login(credentials: LoginCredentials): Promise<ApiResponse<LoginResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.LOGIN, {
      method: 'POST',
      body: credentials,
    })
  },

  async register(data: RegisterData): Promise<ApiResponse<LoginResponse>> {
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

  // ── Password ─────────────────────────────────────────────

  async forgotPassword(email: string): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.FORGOT_PASSWORD, {
      method: 'POST',
      body: { email },
    })
  },

  async resetPassword(data: ResetPasswordData): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.RESET_PASSWORD, {
      method: 'POST',
      body: data,
    })
  },

  async changePassword(data: ChangePasswordData): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.CHANGE_PASSWORD, {
      method: 'POST',
      body: data,
    })
  },

  // ── Email Verification ───────────────────────────────────

  async verifyEmail(id: string, hash: string, query: Record<string, string>): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.VERIFY_EMAIL(id, hash), {
      params: query,
    })
  },

  async resendVerificationEmail(): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.RESEND_VERIFICATION, {
      method: 'POST',
    })
  },
}
