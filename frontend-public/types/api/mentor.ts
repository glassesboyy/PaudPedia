/**
 * Mentor Types
 */
import type { PaginationParams } from './common'

export interface Mentor {
  id: number
  name: string
  slug: string
  bio: string
  avatar_url: string
  expertise: string[]
  total_courses: number
  total_students: number
  rating: number
  created_at: string
  updated_at: string
}

export interface MentorListParams extends PaginationParams {
  expertise?: string
}
