import { http as api } from '@/services/http/client'
import { ENDPOINTS } from '@/services/http/endpoints'
import type { ApiResponse, PaginatedResponse, Student } from '@/types'

export const studentService = {
  getStudents(schoolId: number, params?: any): Promise<PaginatedResponse<Student>> {
    return api.get(ENDPOINTS.STUDENTS.LIST(schoolId), { params })
  },

  getStudent(schoolId: number, studentId: number): Promise<ApiResponse<Student>> {
    return api.get(ENDPOINTS.STUDENTS.DETAIL(schoolId, studentId))
  },

  createStudent(schoolId: number, data: FormData): Promise<ApiResponse<Student>> {
    return api.post(ENDPOINTS.STUDENTS.LIST(schoolId), data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  updateStudent(schoolId: number, studentId: number, data: FormData): Promise<ApiResponse<Student>> {
    return api.post(ENDPOINTS.STUDENTS.DETAIL(schoolId, studentId), data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  deleteStudent(schoolId: number, studentId: number): Promise<ApiResponse<null>> {
    return api.delete(ENDPOINTS.STUDENTS.DETAIL(schoolId, studentId))
  },
}
