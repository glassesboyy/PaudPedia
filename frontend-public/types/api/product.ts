/**
 * Product Types
 */
import type { Category, PaginationParams } from './common'

export interface Product {
  id: number
  title: string
  slug: string
  description: string
  thumbnail_url: string
  price: number
  original_price: number | null
  type: 'digital' | 'physical'
  is_published: boolean
  category: Category
  created_at: string
  updated_at: string
}

export interface ProductListParams extends PaginationParams {
  category?: string
  type?: 'digital' | 'physical'
  min_price?: number
  max_price?: number
}
