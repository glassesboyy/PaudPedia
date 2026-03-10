/**
 * Mentor Types
 *
 * Matches the actual API response from:
 * - GET /api/v1/mentors        → MentorResource (list item)
 * - GET /api/v1/mentors/{id}    → MentorDetailResource
 * - GET /api/v1/mentors/featured → MentorResource (featured list)
 */
import type { PaginationParams } from './common'

/** Mentor list item — returned by GET /mentors and GET /mentors/featured */
export interface Mentor {
  id: number
  name: string
  full_title: string
  title: string
  bio: string | null
  photo_url: string | null
  expertise: string
  courses_count: number
  webinars_count: number
}

/** Alias kept for backward-compat with About page */
export type FeaturedMentor = Mentor

/** Course summary nested inside MentorDetail */
export interface MentorCourse {
  id: number
  title: string
  slug: string
  thumbnail_url: string | null
  price: number
  level: string
  category: {
    id: number
    name: string
    slug: string
  } | null
}

/** Webinar summary nested inside MentorDetail */
export interface MentorWebinar {
  id: number
  title: string
  slug: string
  thumbnail_url: string | null
  price: number
  scheduled_at: string | null
  scheduled_date: string | null
}

/** Full mentor detail — returned by GET /mentors/{id} */
export interface MentorDetail {
  id: number
  name: string
  full_title: string
  title: string
  bio: string | null
  photo_url: string | null
  expertise: string
  social_media: Record<string, string>
  courses_count: number
  webinars_count: number
  courses: MentorCourse[]
  webinars: MentorWebinar[]
  created_at: string
}

export interface MentorListParams extends PaginationParams {
  expertise?: string
}
