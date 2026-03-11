/**
 * Course Types
 *
 * Aligned with backend CourseResource / CourseDetailResource.
 */
import type { Category, PaginationParams } from './common'

// ── Mentor (embedded in course list response) ────────────
export interface CourseMentor {
  id: number
  name: string
  title: string | null
  photo_url: string | null
}

export interface CourseMentorDetail extends CourseMentor {
  bio: string | null
  expertise: string | null
  social_media: Record<string, string> | null
}

// ── Lesson & Module ──────────────────────────────────────
export interface Lesson {
  id: number
  title: string
  type: string | null
  duration_minutes: number | null
  order: number
  is_preview: boolean
}

export interface Module {
  id: number
  title: string
  description: string | null
  order: number
  lessons_count: number
  lessons: Lesson[]
}

// ── Course (list item from CourseResource) ───────────────
export interface Course {
  id: number
  title: string
  slug: string
  description: string | null
  thumbnail_url: string | null
  price: number
  original_price: number | null
  has_discount: boolean
  discount_percentage: number | null
  level: 'beginner' | 'intermediate' | 'advanced' | null
  level_label: string | null
  duration_hours: number | null
  modules_count: number
  mentor: CourseMentor | null
  category: Category | null
  created_at: string
}

// ── CourseDetail (from CourseDetailResource) ─────────────
export interface CourseDetail extends Omit<Course, 'mentor'> {
  enrollments_count: number
  mentor: CourseMentorDetail | null
  modules: Module[]
  updated_at: string
}

// ── Query params ────────────────────────────────────────
export interface CourseListParams extends PaginationParams {
  search?: string
  category?: string
  category_id?: number
  level?: string
  min_price?: number
  max_price?: number
  free?: boolean
  mentor_id?: number
  sort_by?: 'created_at' | 'title' | 'price' | 'duration_hours'
  sort_order?: 'asc' | 'desc'
}
