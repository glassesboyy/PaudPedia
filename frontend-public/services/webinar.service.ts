/**
 * Webinar Service
 */
import type { Webinar, WebinarListParams } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const webinarService = {
  async getList(params?: WebinarListParams): Promise<PaginatedResponse<Webinar>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.WEBINARS.LIST, { params })
  },

  async getBySlug(slug: string): Promise<ApiResponse<Webinar>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.WEBINARS.DETAIL(slug))
  },
}
