/**
 * Webinar Types
 *
 * Matches the actual API responses from:
 * - GET /api/v1/webinars          → WebinarResource (list item)
 * - GET /api/v1/webinars/{slug}   → WebinarDetailResource (detail)
 */
import type { PaginationParams } from './common'

// ── Mentor (embedded in webinar response) ────────────────
export interface WebinarMentor {
  id: number
  name: string
  title: string | null
  photo_url: string | null
}

export interface WebinarMentorDetail extends WebinarMentor {
  bio: string | null
  expertise: string | null
  social_media: Record<string, string> | null
}

// ── Webinar (list item from WebinarResource) ─────────────
export interface Webinar {
  id: number
  title: string
  slug: string
  description: string | null
  thumbnail_url: string | null
  price: number
  original_price: number | null
  has_discount: boolean
  discount_percentage: number | null
  is_owned?: boolean
  scheduled_at: string | null
  scheduled_date: string | null
  scheduled_time: string | null
  duration_minutes: number | null
  max_participants: number | null
  is_upcoming: boolean
  mentor: WebinarMentor | null
  created_at: string
}

// ── WebinarDetail (from WebinarDetailResource) ───────────
export interface WebinarDetail extends Omit<Webinar, 'mentor'> {
  scheduled_day: string | null
  is_past: boolean
  mentor: WebinarMentorDetail | null
  updated_at: string
}

// ── Query params ────────────────────────────────────────
export interface WebinarListParams extends PaginationParams {
  search?: string
  upcoming?: boolean
  past?: boolean
  free?: boolean
  min_price?: number
  max_price?: number
  mentor_id?: number
  start_date?: string
  end_date?: string
  sort_by?: 'scheduled_at' | 'title' | 'price' | 'created_at'
  sort_order?: 'asc' | 'desc'
}
