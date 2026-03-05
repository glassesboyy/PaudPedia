import { z } from 'zod'

// ── Update Profile ───────────────────────────────────────
export const updateProfileSchema = z.object({
  name: z
    .string()
    .min(1, 'Nama wajib diisi')
    .min(3, 'Nama minimal 3 karakter')
    .max(255, 'Nama maksimal 255 karakter'),
  phone: z
    .string()
    .max(20, 'Nomor telepon maksimal 20 karakter')
    .regex(/^[0-9+\-\s()]*$/, 'Format nomor telepon tidak valid')
    .optional()
    .or(z.literal('')),
  gender: z.enum(['male', 'female']).optional().nullable(),
  date_of_birth: z
    .string()
    .optional()
    .or(z.literal(''))
    .refine(
      (val) => {
        if (!val) return true
        const date = new Date(val)
        return !isNaN(date.getTime()) && date < new Date()
      },
      { message: 'Tanggal lahir harus sebelum hari ini' },
    ),
  address: z
    .string()
    .max(1000, 'Alamat maksimal 1000 karakter')
    .optional()
    .or(z.literal('')),
})

// ── Inferred form types ──────────────────────────────────
export type UpdateProfileFormData = z.infer<typeof updateProfileSchema>
