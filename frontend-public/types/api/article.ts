/**
 * Article Types
 */
import type { Category, PaginationParams } from './common'

export interface Article {
  id: number
  title: string
  slug: string
  excerpt: string
  content: string
  thumbnail_url: string
  author: {
    id: number
    name: string
    avatar_url?: string | null
  }
  category: Category
  tags: string[]
  published_at: string
  created_at: string
  updated_at: string
}

export interface ArticleListParams extends PaginationParams {
  category?: string
  tag?: string
}
