export interface LmsLessonSummary {
  id: number
  slug: string
  title: string
  type: 'video' | 'pdf' | 'text' | string | null
  duration_minutes: number | null
  order: number
  is_completed: boolean
}

export interface LmsModule {
  id: number
  title: string
  description: string | null
  order: number
  lessons: LmsLessonSummary[]
}

export interface LmsProgress {
  total_lessons: number
  completed_lessons: number
  progress_percentage: number
  is_completed: boolean
  completed_at: string | null
}

export interface LmsCoursePlayerData {
  course: {
    id: number
    title: string
    slug: string
    thumbnail_url: string | null
    mentor: {
      id: number
      name: string
    } | null
    modules: LmsModule[]
  }
  progress: LmsProgress
  next_lesson_slug: string | null
  certificate: {
    available: boolean
    download_url: string | null
  }
}

export interface LmsLessonDetail {
  id: number
  slug: string
  title: string
  type: 'video' | 'pdf' | 'text' | string | null
  duration_minutes: number | null
  video_url: string | null
  text_content: string | null
  pdf_url: string | null
}

export interface LmsMarkCompleteResponse {
  lesson_id: number
  is_completed: boolean
  progress: LmsProgress
  certificate: {
    available: boolean
    download_url: string | null
  }
}

export interface LmsCertificateResponse {
  available: boolean
  download_url: string | null
}
