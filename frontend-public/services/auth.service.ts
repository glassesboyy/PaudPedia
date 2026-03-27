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
  RegisterSchoolData,
  RegisterSchoolUpgradeData,
  ResetPasswordData,
  UpdateProfileData,
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

  async registerSchool(data: RegisterSchoolData): Promise<ApiResponse<LoginResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.REGISTER_SCHOOL, {
      method: 'POST',
      body: data,
    })
  },

  async registerSchoolUpgrade(data: RegisterSchoolUpgradeData): Promise<ApiResponse<LoginResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.SCHOOLS.REGISTER, {
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
    const response = await apiFetch<ApiResponse<{ user: User }>>(API_ENDPOINTS.AUTH.ME)
    return response.data.user
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

  // ── Profile ──────────────────────────────────────────────

  async getProfile(): Promise<ApiResponse<User>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.PROFILE)
  },

  async updateProfile(data: UpdateProfileData): Promise<ApiResponse<User>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.PROFILE, {
      method: 'PUT',
      body: data,
    })
  },

  async uploadAvatar(file: File): Promise<ApiResponse<User>> {
    const apiFetch = useApiFetch()
    const formData = new FormData()
    formData.append('avatar', file)
    return apiFetch(API_ENDPOINTS.AUTH.PROFILE_AVATAR, {
      method: 'POST',
      body: formData,
    })
  },

  async removeAvatar(): Promise<ApiResponse<User>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.AUTH.PROFILE_AVATAR, {
      method: 'DELETE',
    })
  },
}
