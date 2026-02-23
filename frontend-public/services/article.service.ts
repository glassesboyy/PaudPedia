/**
 * Article Service
 */
import type { Article, ArticleListParams } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const articleService = {
  async getList(params?: ArticleListParams): Promise<PaginatedResponse<Article>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.LIST, { params })
  },

  async getBySlug(slug: string): Promise<ApiResponse<Article>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.DETAIL(slug))
  },

  async getFeatured(): Promise<ApiResponse<Article[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.FEATURED)
  },

  async getRelated(slug: string): Promise<ApiResponse<Article[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ARTICLES.RELATED(slug))
  },
}
