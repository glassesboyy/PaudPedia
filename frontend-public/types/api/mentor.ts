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
  social_media_links?: Record<string, string>
  created_at: string
  updated_at: string
}

/** Lightweight mentor returned by GET /mentors/featured */
export interface FeaturedMentor {
  id: number
  name: string
  full_title: string
  title: string
  bio: string
  photo_url: string | null
  expertise: string
  courses_count: number
  webinars_count: number
}

export interface MentorListParams extends PaginationParams {
  expertise?: string
}
