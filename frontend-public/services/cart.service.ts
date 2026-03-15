/**
 * Cart Service
 *
 * Handles promo code validation against the backend.
 * Cart item management is client-side only (Pinia store).
 */
import type { PromoValidationResponse } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const cartService = {
  async validatePromo(code: string, subtotal: number): Promise<ApiResponse<PromoValidationResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.VALIDATE_PROMO, {
      method: 'POST',
      body: { code, subtotal },
    })
  },
}
