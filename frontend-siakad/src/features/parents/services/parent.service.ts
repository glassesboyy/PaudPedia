import { http as api } from '@/services/http/client'
import { ENDPOINTS } from '@/services/http/endpoints'
import type { ApiResponse, PaginatedResponse, ParentProfile } from '@/types'

export const parentService = {
  getParents(schoolId: number, params?: Record<string, any>): Promise<PaginatedResponse<ParentProfile>> {
    return api.get(ENDPOINTS.PARENTS.LIST(schoolId), { params })
  },

  getParent(schoolId: number, parentId: number): Promise<ApiResponse<ParentProfile>> {
    return api.get(ENDPOINTS.PARENTS.DETAIL(schoolId, parentId))
  },

  createParent(schoolId: number, data: Partial<ParentProfile>): Promise<ApiResponse<ParentProfile>> {
    return api.post(ENDPOINTS.PARENTS.LIST(schoolId), data)
  },

  updateParent(schoolId: number, parentId: number, data: Partial<ParentProfile>): Promise<ApiResponse<ParentProfile>> {
    return api.put(ENDPOINTS.PARENTS.DETAIL(schoolId, parentId), data)
  },

  deleteParent(schoolId: number, parentId: number): Promise<ApiResponse<null>> {
    return api.delete(ENDPOINTS.PARENTS.DETAIL(schoolId, parentId))
  },
}
