/**
 * Auth Types
 */

export interface User {
  id: number
  name: string
  email: string
  avatar_url?: string | null
  email_verified_at?: string | null
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
  remember?: boolean
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface ForgotPasswordData {
  email: string
}

export interface ResetPasswordData {
  token: string
  email: string
  password: string
  password_confirmation: string
}

export interface ChangePasswordData {
  current_password: string
  password: string
  password_confirmation: string
}

/** Response from login / register endpoints */
export interface AuthTokenResponse {
  user: User
  token: string
  token_type: string
}

/** Login-specific response includes email_verified flag */
export interface LoginResponse extends AuthTokenResponse {
  email_verified: boolean
}
