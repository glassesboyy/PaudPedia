/**
 * User Dashboard Types
 *
 * Matches the response structure from GET /api/v1/user/* endpoints.
 */

// ── My Courses (FR-UA-08) ────────────────────────────────
export interface UserCourseMentor {
  id: number
  name: string
  photo_url: string | null
}

export interface UserCourse {
  id: number
  course_id: number
  title: string
  slug: string
  thumbnail_url: string | null
  level: 'beginner' | 'intermediate' | 'advanced' | null
  level_label: string | null
  mentor: UserCourseMentor | null
  progress_percentage: number
  total_lessons: number
  completed_lessons: number
  is_completed: boolean
  first_lesson_id: number | null
  enrolled_at: string
  completed_at: string | null
}

// ── My Products (FR-UA-09) ───────────────────────────────
export interface UserProductFileInfo {
  type: string
}

export interface UserProduct {
  id: number
  product_id: number | null
  title: string
  slug: string | null
  thumbnail_url: string | null
  category: { id: number; name: string } | null
  file_info: UserProductFileInfo | null
  download_url: string | null
  purchase_date: string | null
  purchase_date_formatted: string | null
}

// ── My Webinars (FR-UA-10) ───────────────────────────────
export interface UserWebinarMentor {
  id: number
  name: string
  photo_url: string | null
}

export interface UserWebinar {
  id: number
  webinar_id: number | null
  title: string
  slug: string | null
  thumbnail_url: string | null
  scheduled_at: string | null
  scheduled_date: string | null
  scheduled_time: string | null
  duration_minutes: number | null
  status: 'upcoming' | 'finished' | 'unknown'
  status_label: string
  zoom_link: string | null
  mentor: UserWebinarMentor | null
}

// ── My Certificates (FR-UA-11) ───────────────────────────
export interface UserCertificate {
  id: number
  course_id: number | null
  course_title: string | null
  course_slug: string | null
  issue_date: string | null
  issue_date_formatted: string | null
  certificate_url: string | null
  download_url: string | null
}

// ── Transaction History (FR-UA-12) ───────────────────────
export interface TransactionItem {
  id: number
  type: 'course' | 'webinar' | 'product'
  type_label: string
  title: string
  price: number
  quantity: number
  subtotal: number
}

export interface Transaction {
  id: number
  order_number: string
  status: 'pending' | 'paid' | 'failed' | 'cancelled' | 'expired'
  status_label: string
  status_color: string
  total_amount: number
  discount_amount: number
  final_amount: number
  payment_method: string | null
  payment_url?: string | null
  items: TransactionItem[]
  paid_at: string | null
  created_at: string
  created_date: string
}
