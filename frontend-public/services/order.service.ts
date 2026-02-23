/**
 * Order Service
 */
import type { Order, OrderListParams } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse, PaginatedResponse } from './api/types'

export const orderService = {
  async getList(params?: OrderListParams): Promise<PaginatedResponse<Order>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ORDERS.LIST, { params })
  },

  async getById(id: number): Promise<ApiResponse<Order>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ORDERS.DETAIL(id))
  },

  async create(body: Record<string, unknown>): Promise<ApiResponse<Order>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.ORDERS.CREATE, { method: 'POST', body })
  },
}
