/**
 * Shared API response type definitions.
 *
 * These mirror the JSON envelope the Laravel backend returns.
 */

/** Standard single-resource response */
export interface ApiResponse<T> {
  success: boolean
  message: string | null
  data: T
}

/** Paginated list response */
export interface PaginatedResponse<T> {
  success: boolean
  message: string | null
  data: T[]
  meta: PaginationMeta
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

/** Validation error shape (422) */
export interface ValidationErrorResponse {
  message: string
  errors: Record<string, string[]>
}
