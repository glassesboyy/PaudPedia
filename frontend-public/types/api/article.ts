/**
 * Article Types
 */
import type { Category, PaginationParams } from './common'

export interface ArticleAuthor {
  id: number
  name: string
  avatar_url?: string | null
  email?: string
}

/** List-level article (from ArticleResource) */
export interface Article {
  id: number
  title: string
  slug: string
  excerpt: string
  content?: string
  featured_image_url: string | null
  /** @deprecated Use featured_image_url */
  thumbnail_url?: string
  author: ArticleAuthor
  category: Category
  tags: string[]
  view_count: number
  reading_time: number
  is_featured: boolean
  published_at: string
  published_date: string
  created_at?: string
  updated_at?: string
}

/** Detail-level article (from ArticleDetailResource) */
export interface ArticleDetail extends Article {
  content: string
  meta: {
    title: string
    description: string
    keywords: string[] | string
  }
}

/** Show endpoint response data shape */
export interface ArticleShowData {
  article: ArticleDetail
  related_articles: Article[]
}

export interface ArticleListParams extends PaginationParams {
  category?: string
  tag?: string
  featured?: boolean
}
