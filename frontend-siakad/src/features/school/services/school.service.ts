import { http } from '@/services/http/client'
import { ENDPOINTS } from '@/services/http/endpoints'
import type { ApiResponse, School } from '@/types'

export const schoolService = {
  /**
   * Get school profile details.
   */
  async getProfile(schoolId: number): Promise<ApiResponse<School>> {
    return http.get(ENDPOINTS.SCHOOL.PROFILE(schoolId))
  },

  /**
   * Update school profile details.
   */
  async updateProfile(schoolId: number, data: FormData): Promise<ApiResponse<School>> {
    // We use common FormData for logo upload support
    return http.post(ENDPOINTS.SCHOOL.PROFILE(schoolId), data)
  },
}
