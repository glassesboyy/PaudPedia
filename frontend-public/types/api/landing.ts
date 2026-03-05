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

export interface HeroData {
  title: string
  subtitle: string
  image: string | null
  cta_text: string
  cta_link: string
}

export interface ContactInfo {
  email: string
  phone: string
  whatsapp: string
  whatsapp_link?: string
  address: string
  social_media?: SocialMedia
}

export interface SocialMedia {
  instagram?: string
  facebook?: string
  youtube?: string
  linkedin?: string
  twitter?: string
  tiktok?: string
}

export interface FooterData {
  copyright: string
  description: string
}

export interface SiteSettings {
  site_name: string
  site_tagline: string
  site_logo: string | null
  site_favicon?: string | null
  hero: HeroData
  contact: ContactInfo
  social_media: SocialMedia
  footer: FooterData
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
