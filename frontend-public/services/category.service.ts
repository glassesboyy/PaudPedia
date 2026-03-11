/**
 * Category Service
 */
import type { Category } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const categoryService = {
  async getList(): Promise<ApiResponse<Category[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CATEGORIES.LIST)
  },

  async getArticleCategories(): Promise<ApiResponse<Category[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CATEGORIES.ARTICLES)
  },

  async getProductCategories(): Promise<ApiResponse<Category[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CATEGORIES.PRODUCTS)
  },

  async getCourseCategories(): Promise<ApiResponse<Category[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CATEGORIES.COURSES)
  },
}
