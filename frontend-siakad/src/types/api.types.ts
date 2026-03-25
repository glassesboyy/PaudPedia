export interface ApiResponse<T> {
  success: boolean
  message: string | null
  data: T
}

export interface PaginatedResponse<T> {
  success: boolean
  message: string | null
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
}

export interface ValidationError {
  message: string
  errors: Record<string, string[]>
}
