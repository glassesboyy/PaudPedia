/**
 * Mentor Service
 */
import type { FeaturedMentor, Mentor, MentorListParams } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const mentorService = {
  async getList(params?: MentorListParams): Promise<PaginatedResponse<Mentor>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.MENTORS.LIST, { params })
  },

  async getBySlug(slug: string): Promise<ApiResponse<Mentor>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.MENTORS.DETAIL(slug))
  },

  async getFeatured(): Promise<ApiResponse<FeaturedMentor[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.MENTORS.FEATURED)
  },
}
