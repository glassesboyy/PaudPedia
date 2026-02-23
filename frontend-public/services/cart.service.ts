/**
 * Cart Service
 */
import type { CartItem } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const cartService = {
  async get(): Promise<ApiResponse<CartItem[]>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.GET)
  },

  async addItem(body: { itemable_id: number; itemable_type: string }): Promise<ApiResponse<CartItem>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.ADD, { method: 'POST', body })
  },

  async removeItem(itemId: number): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.REMOVE(itemId), { method: 'DELETE' })
  },

  async validatePromo(code: string, items: CartItem[]): Promise<ApiResponse<{ discount: number }>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.VALIDATE_PROMO, {
      method: 'POST',
      body: { code, items },
    })
  },
}
