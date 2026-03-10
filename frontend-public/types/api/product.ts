/**
 * Product Types
 *
 * Aligned with backend ProductResource / ProductDetailResource.
 */
import type { Category, PaginationParams } from './common'

export interface ProductFileInfo {
  type: string
  size: number | null
  size_formatted: string | null
}

/**
 * Product (list item) — returned by ProductResource
 */
export interface Product {
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
  category: Category | null
  created_at: string
}

/**
 * Product (detail) — returned by ProductDetailResource
 * Same as Product but description is full HTML, includes updated_at.
 */
export interface ProductDetail extends Product {
  updated_at: string
}

export interface ProductListParams extends PaginationParams {
  category?: string
  category_id?: number
  min_price?: number
  max_price?: number
  free?: boolean
  sort_by?: 'created_at' | 'title' | 'price'
  sort_order?: 'asc' | 'desc'
}
