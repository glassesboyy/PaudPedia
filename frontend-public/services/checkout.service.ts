/**
 * Checkout Service
 *
 * Handles order creation and Midtrans snap token retrieval.
 */
import type { CheckoutResponse } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export interface CheckoutPayload {
  items: Array<{ id: number; type: string; quantity: number }>
  promo_code?: string | null
}

export const checkoutService = {
  async create(payload: CheckoutPayload): Promise<ApiResponse<CheckoutResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CHECKOUT.CREATE, {
      method: 'POST',
      body: payload,
    })
  },
}
