/**
 * Auth Types
 */

export interface User {
  id: number
  name: string
  email: string
  phone?: string | null
  avatar_url?: string | null
  gender?: 'male' | 'female' | null
  date_of_birth?: string | null
  address?: string | null
  email_verified_at?: string | null
  is_active?: boolean
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

/** Data for updating user profile (text fields only) */
export interface UpdateProfileData {
  name: string
  phone?: string
  gender?: string
  date_of_birth?: string
  address?: string
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
