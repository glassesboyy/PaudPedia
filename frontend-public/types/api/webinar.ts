/**
 * Webinar Types
 */
import type { Category, PaginationParams } from './common'
import type { Mentor } from './mentor'

export interface Webinar {
  id: number
  title: string
  slug: string
  description: string
  thumbnail_url: string
  price: number
  original_price: number | null
  start_date: string
  end_date: string
  is_published: boolean
  mentor: Mentor
  category: Category
  created_at: string
  updated_at: string
}

export interface WebinarListParams extends PaginationParams {
  category?: string
  status?: 'upcoming' | 'live' | 'past'
}
