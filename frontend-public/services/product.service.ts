/**
 * Product Service
 */
import type { Product, ProductListParams } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const productService = {
  async getList(params?: ProductListParams): Promise<PaginatedResponse<Product>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.PRODUCTS.LIST, { params })
  },

  async getBySlug(slug: string): Promise<ApiResponse<Product>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.PRODUCTS.DETAIL(slug))
  },
}
