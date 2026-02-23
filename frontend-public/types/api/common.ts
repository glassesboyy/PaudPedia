/**
 * Common / Shared Types
 */

export interface Category {
  id: number
  name: string
  slug: string
  description?: string
  icon?: string
  parent_id?: number | null
}

export interface PaginationParams {
  page?: number
  per_page?: number
  search?: string
  sort_by?: string
  sort_order?: 'asc' | 'desc'
}
