/**
 * Landing Page Types
 *
 * Matches the optimized response structure from GET /api/v1/landing.
 * Uses landing-specific types that contain only fields rendered on the landing page.
 */
import type { Category } from './common'
import type { CourseMentor } from './course'
import type { ProductFileInfo } from './product'
import type { WebinarMentor } from './webinar'

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

/** Only the 4 stats shown in HeroSection */
export interface PlatformStatistics {
  total_users: number
  total_courses: number
  total_webinars: number
  total_articles: number
}

// ── Landing-specific item types ──────────────────────────
// These contain only the fields returned by the landing endpoint.
// The full Course/Webinar/etc. types remain for list & detail pages.

export interface LandingCourse {
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
  level: 'beginner' | 'intermediate' | 'advanced' | null
  level_label: string | null
  duration_hours: number | null
  modules_count: number
  mentor: CourseMentor | null
  category: Pick<Category, 'id' | 'name'> | null
}

export interface LandingWebinar {
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
  scheduled_date: string | null
  scheduled_time: string | null
  duration_minutes: number | null
  max_participants: number | null
  is_upcoming: boolean
  mentor: WebinarMentor | null
}

export interface LandingProduct {
  id: number
  title: string
  slug: string
  description: string | null
  thumbnail_url: string | null
  price: number
  original_price: number | null
  has_discount: boolean
  discount_percentage: number | null
  file_info: ProductFileInfo | null
  category: Pick<Category, 'id' | 'name'> | null
}

export interface LandingTestimonial {
  id: number
  name: string
  title: string
  content: string
  rating: number
  photo_url: string | null
}

export interface LandingArticle {
  id: number
  title: string
  slug: string
  excerpt: string
  featured_image_url: string | null
  tags: string[]
  reading_time: number
  is_featured: boolean
  author: { id: number; name: string }
  category: Pick<Category, 'id' | 'name'>
  published_at: string
  published_date: string
}

export interface LandingPageData {
  settings: SiteSettings
  statistics: PlatformStatistics
  featured_courses: LandingCourse[]
  featured_webinars: LandingWebinar[]
  featured_products: LandingProduct[]
  testimonials: LandingTestimonial[]
  latest_articles: LandingArticle[]
}
