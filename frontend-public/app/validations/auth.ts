import { z } from 'zod'

// ── Login ────────────────────────────────────────────────
export const loginSchema = z.object({
  email: z.string().min(1, 'Email wajib diisi').email('Format email tidak valid'),
  password: z.string().min(1, 'Password wajib diisi'),
})

// ── Register ─────────────────────────────────────────────
export const registerSchema = z
  .object({
    name: z
      .string()
      .min(1, 'Nama wajib diisi')
      .min(3, 'Nama minimal 3 karakter')
      .max(100, 'Nama maksimal 100 karakter'),
    email: z.string().min(1, 'Email wajib diisi').email('Format email tidak valid'),
    password: z.string().min(1, 'Password wajib diisi').min(8, 'Password minimal 8 karakter'),
    password_confirmation: z.string().min(1, 'Konfirmasi password wajib diisi'),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: 'Konfirmasi password tidak cocok',
    path: ['password_confirmation'],
  })

// ── Register School (guest — user + school) ──────────────
export const registerSchoolSchema = z
  .object({
    name: z
      .string()
      .min(1, 'Nama wajib diisi')
      .min(3, 'Nama minimal 3 karakter')
      .max(100, 'Nama maksimal 100 karakter'),
    email: z.string().min(1, 'Email wajib diisi').email('Format email tidak valid'),
    password: z.string().min(1, 'Password wajib diisi').min(8, 'Password minimal 8 karakter'),
    password_confirmation: z.string().min(1, 'Konfirmasi password wajib diisi'),
    school_name: z
      .string()
      .min(1, 'Nama sekolah wajib diisi')
      .min(3, 'Nama sekolah minimal 3 karakter'),
    school_npsn: z
      .string()
      .min(1, 'NPSN wajib diisi')
      .regex(/^\d{8}$/, 'NPSN harus 8 digit angka'),
    school_address: z
      .string()
      .min(1, 'Alamat sekolah wajib diisi')
      .min(10, 'Alamat minimal 10 karakter'),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: 'Konfirmasi password tidak cocok',
    path: ['password_confirmation'],
  })

// ── Register School (authenticated user upgrade) ─────────
export const registerSchoolUpgradeSchema = z.object({
  school_name: z
    .string()
    .min(1, 'Nama sekolah wajib diisi')
    .min(3, 'Nama sekolah minimal 3 karakter'),
  school_npsn: z
    .string()
    .min(1, 'NPSN wajib diisi')
    .regex(/^\d{8}$/, 'NPSN harus 8 digit angka'),
  school_address: z
    .string()
    .min(1, 'Alamat sekolah wajib diisi')
    .min(10, 'Alamat minimal 10 karakter'),
})

// ── Forgot Password ──────────────────────────────────────
export const forgotPasswordSchema = z.object({
  email: z.string().min(1, 'Email wajib diisi').email('Format email tidak valid'),
})

// ── Reset Password ───────────────────────────────────────
export const resetPasswordSchema = z
  .object({
    email: z.string().min(1, 'Email wajib diisi').email('Format email tidak valid'),
    password: z.string().min(1, 'Password wajib diisi').min(8, 'Password minimal 8 karakter'),
    password_confirmation: z.string().min(1, 'Konfirmasi password wajib diisi'),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: 'Konfirmasi password tidak cocok',
    path: ['password_confirmation'],
  })

// ── Change Password ──────────────────────────────────────
export const changePasswordSchema = z
  .object({
    current_password: z.string().min(1, 'Password saat ini wajib diisi'),
    password: z.string().min(1, 'Password baru wajib diisi').min(8, 'Password baru minimal 8 karakter'),
    password_confirmation: z.string().min(1, 'Konfirmasi password wajib diisi'),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: 'Konfirmasi password tidak cocok',
    path: ['password_confirmation'],
  })

// ── Helpers ──────────────────────────────────────────────

/** Flatten ZodError issues into a `{ fieldName: firstMessage }` map */
export function extractZodErrors(error: z.ZodError): Record<string, string> {
  const errors: Record<string, string> = {}
  for (const issue of error.issues) {
    const key = issue.path.join('.')
    if (!errors[key]) errors[key] = issue.message
  }
  return errors
}

/** Extract first error per field from a Laravel 422 response `errors` object */
export function extractApiErrors(apiErrors: Record<string, string[]>): Record<string, string> {
  const result: Record<string, string> = {}
  const entries = Object.entries(apiErrors)
  for (let i = 0; i < entries.length; i++) {
    const key = entries[i]![0]
    const messages = entries[i]![1]
    if (messages && messages.length > 0 && messages[0]) {
      result[key] = messages[0]
    }
  }
  return result
}

/**
 * Parse a catch-block error from `$fetch` into field-level and general errors.
 * Returns `{ fieldErrors, message }` — one will be populated based on the response shape.
 */
export function parseApiError(err: unknown): { fieldErrors: Record<string, string>; message: string } {
  const data = (err as { data?: { message?: string; errors?: Record<string, string[]> } } | undefined)?.data
  if (data?.errors) {
    return { fieldErrors: extractApiErrors(data.errors), message: '' }
  }
  if (data?.message) {
    return { fieldErrors: {}, message: data.message }
  }
  return { fieldErrors: {}, message: 'Terjadi kesalahan. Silakan coba lagi.' }
}

// ── Inferred form types ──────────────────────────────────
export type LoginFormData = z.infer<typeof loginSchema>
export type RegisterFormData = z.infer<typeof registerSchema>
export type ForgotPasswordFormData = z.infer<typeof forgotPasswordSchema>
export type ResetPasswordFormData = z.infer<typeof resetPasswordSchema>
export type ChangePasswordFormData = z.infer<typeof changePasswordSchema>
