/**
 * Mentor Service
 */
import type { Mentor, MentorDetail, MentorListParams } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const mentorService = {
  async getList(params?: MentorListParams): Promise<PaginatedResponse<Mentor>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.MENTORS.LIST, { params })
  },

  async getById(id: number): Promise<ApiResponse<MentorDetail>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.MENTORS.DETAIL(id))
  },

  async getFeatured(): Promise<ApiResponse<Mentor[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.MENTORS.FEATURED)
  },
}
