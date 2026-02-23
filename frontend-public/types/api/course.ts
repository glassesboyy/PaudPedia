/**
 * Course Types
 */
import type { Category, PaginationParams } from './common'
import type { Mentor } from './mentor'

export interface Course {
  id: number
  title: string
  slug: string
  description: string
  thumbnail_url: string
  price: number
  original_price: number | null
  level: 'beginner' | 'intermediate' | 'advanced'
  duration_hours: number
  is_published: boolean
  modules_count: number
  mentor: Mentor
  category: Category
  modules?: Module[]
  created_at: string
  updated_at: string
}

export interface Module {
  id: number
  title: string
  order: number
  lessons: Lesson[]
}

export interface Lesson {
  id: number
  title: string
  slug: string
  duration_minutes: number
  is_preview: boolean
  order: number
}

export interface CourseListParams extends PaginationParams {
  category?: string
  level?: string
  min_price?: number
  max_price?: number
}
