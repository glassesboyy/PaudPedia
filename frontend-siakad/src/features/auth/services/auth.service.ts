import { http } from '@/services/http/client'
import { ENDPOINTS } from '@/services/http/endpoints'
import type { ApiResponse, User, SchoolMembership } from '@/types'

/* ── Request DTOs ─────────────────────────────────────── */

export interface LoginPayload {
  email: string
  password: string
}

export interface RegisterSchoolPayload {
  name: string
  npsn: string
  address: string
}

/* ── Response DTOs ────────────────────────────────────── */

export interface LoginResponse {
  user: User
  token: string
}

export interface MeResponse {
  user: User
}

/* ── Auth Service ─────────────────────────────────────── */

export const authService = {
  /**
   * Login with email + password → returns user + auth token.
   */
  async login(payload: LoginPayload): Promise<ApiResponse<LoginResponse>> {
    return http.post(ENDPOINTS.AUTH.LOGIN, payload)
  },

  /**
   * Register a new school (requires authenticated user).
   */
  async registerSchool(payload: RegisterSchoolPayload): Promise<ApiResponse<{ school_id: number }>> {
    return http.post(ENDPOINTS.AUTH.REGISTER_SCHOOL, payload)
  },

  /**
   * Get current authenticated user.
   */
  async fetchMe(): Promise<ApiResponse<MeResponse>> {
    return http.get(ENDPOINTS.AUTH.ME)
  },

  /**
   * Logout → invalidates current token on server.
   */
  async logout(): Promise<void> {
    return http.post(ENDPOINTS.AUTH.LOGOUT)
  },

  /**
   * Get all school memberships for the current user.
   */
  async fetchMemberships(): Promise<ApiResponse<SchoolMembership[]>> {
    return http.get(ENDPOINTS.SCHOOL.MEMBERSHIPS)
  },
}
