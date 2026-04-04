import { http as api } from '@/services/http/client'
import { ENDPOINTS } from '@/services/http/endpoints'
import type { ApiResponse, PaginatedResponse, ClassRoom } from '@/types'

export const classService = {
  getClasses(schoolId: number, params?: any): Promise<PaginatedResponse<ClassRoom>> {
    return api.get(ENDPOINTS.CLASSES.LIST(schoolId), { params })
  },

  getClass(schoolId: number, classId: number): Promise<ApiResponse<ClassRoom>> {
    return api.get(ENDPOINTS.CLASSES.DETAIL(schoolId, classId))
  },

  createClass(schoolId: number, data: Partial<ClassRoom>): Promise<ApiResponse<ClassRoom>> {
    return api.post(ENDPOINTS.CLASSES.LIST(schoolId), data)
  },

  updateClass(schoolId: number, classId: number, data: Partial<ClassRoom>): Promise<ApiResponse<ClassRoom>> {
    return api.put(ENDPOINTS.CLASSES.DETAIL(schoolId, classId), data)
  },

  deleteClass(schoolId: number, classId: number): Promise<ApiResponse<null>> {
    return api.delete(ENDPOINTS.CLASSES.DETAIL(schoolId, classId))
  },
}
