import { http } from '@/services/http/client'
import { ENDPOINTS } from '@/services/http/endpoints'
import type { ApiResponse, Operator } from '@/types'

export interface CreateOperatorPayload {
  name: string
  email: string
  phone: string
}

export const operatorService = {
  /**
   * Get list of operators in a school.
   */
  async getOperators(schoolId: number, params?: any): Promise<ApiResponse<{ operators: Operator[], meta: any }>> {
    return http.get(ENDPOINTS.OPERATORS.LIST(schoolId), { params })
  },

  /**
   * Add a new operator to a school.
   */
  async createOperator(schoolId: number, payload: CreateOperatorPayload): Promise<ApiResponse<Operator>> {
    return http.post(ENDPOINTS.OPERATORS.LIST(schoolId), payload)
  },

  /**
   * Get specific operator detail.
   */
  async getOperator(schoolId: number, operatorId: number): Promise<ApiResponse<Operator>> {
    return http.get(ENDPOINTS.OPERATORS.DETAIL(schoolId, operatorId))
  },

  /**
   * Remove an operator from a school.
   */
  async deleteOperator(schoolId: number, operatorId: number): Promise<ApiResponse<null>> {
    return http.delete(ENDPOINTS.OPERATORS.DETAIL(schoolId, operatorId))
  },

  /**
   * Toggle active status of an operator in a school.
   */
  async toggleOperatorActive(schoolId: number, operatorId: number): Promise<ApiResponse<null>> {
    return http.patch(ENDPOINTS.OPERATORS.TOGGLE_ACTIVE(schoolId, operatorId))
  },
}
