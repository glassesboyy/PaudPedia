/**
 * Article Service
 */
import type { Article, ArticleListParams, ArticleShowData } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const articleService = {
  /** Paginated article list with filters */
  async getList(params?: ArticleListParams): Promise<PaginatedResponse<Article>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.LIST, { params })
  },

  /** Article detail + related articles */
  async getBySlug(slug: string): Promise<ApiResponse<ArticleShowData>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.DETAIL(slug))
  },

  /** Featured articles */
  async getFeatured(limit?: number): Promise<ApiResponse<Article[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.FEATURED, {
      params: limit ? { limit } : undefined,
    })
  },

  /** Related articles for a given article slug */
  async getRelated(slug: string): Promise<ApiResponse<Article[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.RELATED(slug))
  },
}
