import { http } from '@/services/http/client'
import { ENDPOINTS } from '@/services/http/endpoints'
import type { ApiResponse, Teacher } from '@/types'

export interface CreateTeacherPayload {
  name: string
  email: string
  nip?: string
  phone?: string
  specialization?: string
}

export const teacherService = {
  /**
   * Get list of teachers in a school.
   */
  async getTeachers(schoolId: number, params?: any): Promise<ApiResponse<{ teachers: Teacher[], meta: any }>> {
    return http.get(ENDPOINTS.TEACHERS.LIST(schoolId), { params })
  },

  /**
   * Add a new teacher to a school.
   */
  async createTeacher(schoolId: number, payload: CreateTeacherPayload): Promise<ApiResponse<Teacher>> {
    return http.post(ENDPOINTS.TEACHERS.LIST(schoolId), payload)
  },

  /**
   * Get specific teacher detail.
   */
  async getTeacher(schoolId: number, teacherId: number): Promise<ApiResponse<Teacher>> {
    return http.get(ENDPOINTS.TEACHERS.DETAIL(schoolId, teacherId))
  },

  /**
   * Remove a teacher from a school.
   */
  async deleteTeacher(schoolId: number, teacherId: number): Promise<ApiResponse<null>> {
    return http.delete(ENDPOINTS.TEACHERS.DETAIL(schoolId, teacherId))
  },
}
