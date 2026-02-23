/**
 * Zod Validation Schemas
 *
 * Reusable validation schemas for forms.
 */
import { z } from 'zod'

export const loginSchema = z.object({
  email: z.string().email('Email tidak valid'),
  password: z.string().min(8, 'Password minimal 8 karakter'),
  remember: z.boolean().optional(),
})

export const registerSchema = z.object({
  name: z.string().min(2, 'Nama minimal 2 karakter').max(100),
  email: z.string().email('Email tidak valid'),
  password: z.string().min(8, 'Password minimal 8 karakter'),
  password_confirmation: z.string().min(8, 'Konfirmasi password minimal 8 karakter'),
}).refine((data) => data.password === data.password_confirmation, {
  message: 'Password tidak cocok',
  path: ['password_confirmation'],
})

export const forgotPasswordSchema = z.object({
  email: z.string().email('Email tidak valid'),
})

export const resetPasswordSchema = z.object({
  email: z.string().email('Email tidak valid'),
  password: z.string().min(8, 'Password minimal 8 karakter'),
  password_confirmation: z.string().min(8, 'Konfirmasi password minimal 8 karakter'),
  token: z.string(),
}).refine((data) => data.password === data.password_confirmation, {
  message: 'Password tidak cocok',
  path: ['password_confirmation'],
})

// Type inference from schemas
export type LoginFormData = z.infer<typeof loginSchema>
export type RegisterFormData = z.infer<typeof registerSchema>
export type ForgotPasswordFormData = z.infer<typeof forgotPasswordSchema>
export type ResetPasswordFormData = z.infer<typeof resetPasswordSchema>
