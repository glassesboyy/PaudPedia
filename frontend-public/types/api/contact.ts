/**
 * Contact Types
 *
 * Matches ContactInfoResource from the backend API.
 */
import type { SocialMedia } from './landing'

export interface ContactPageInfo {
  email: string | null
  phone: string | null
  address: string | null
  social_media: SocialMedia
}

/** Shape for the contact form (frontend-only; uses MailJS) */
export interface ContactFormData {
  name: string
  email: string
  subject: string
  message: string
}
