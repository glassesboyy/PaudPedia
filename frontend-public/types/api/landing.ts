/**
 * Landing Page Types
 *
 * Matches the response structure from GET /api/v1/landing.
 */
import type { Article } from './article'
import type { Course } from './course'
import type { Product } from './product'
import type { Testimonial } from './testimonial'
import type { Webinar } from './webinar'

export interface ContactInfo {
  email: string | null
  phone: string | null
  address: string | null
}

export interface SocialMedia {
  facebook?: string
  instagram?: string
  twitter?: string
  youtube?: string
  tiktok?: string
  linkedin?: string
  telegram?: string
  discord?: string
}

export interface SiteSettings {
  site_name: string
  site_tagline: string | null
  site_description: string | null
  contact: ContactInfo
  social_media: SocialMedia
}

export interface PlatformStatistics {
  total_schools: number
  total_users: number
  total_courses: number
  total_webinars: number
  total_products: number
  total_articles: number
  total_enrollments: number
}

export interface LandingPageData {
  settings: SiteSettings
  statistics: PlatformStatistics
  featured_courses: Course[]
  featured_webinars: Webinar[]
  featured_products: Product[]
  testimonials: Testimonial[]
  latest_articles: Article[]
}
