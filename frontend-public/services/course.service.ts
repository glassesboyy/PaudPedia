/**
 * Course Service
 *
 * API calls for courses.
 */
import type { Course, CourseDetail, CourseListParams } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const courseService = {
  async getList(params?: CourseListParams): Promise<PaginatedResponse<Course>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.COURSES.LIST, { params })
  },

  async getBySlug(slug: string): Promise<ApiResponse<CourseDetail>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.COURSES.DETAIL(slug))
  },

  async getFeatured(limit?: number): Promise<ApiResponse<Course[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.COURSES.FEATURED, { params: limit ? { limit } : undefined })
  },

  async enroll(courseId: number): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.COURSES.ENROLL(courseId), { method: 'POST' })
  },

  async getProgress(courseId: number): Promise<ApiResponse<unknown>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.COURSES.PROGRESS(courseId))
  },
}
