/**
 * Cart Service
 *
 * Server-backed cart operations. All cart state lives on the server;
 * the Pinia store acts as a local cache.
 */
import type { CartItem, PromoValidationResponse } from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export interface CartResponse {
  items: CartItem[]
  subtotal: number
}

export const cartService = {
  /** Fetch the authenticated user's cart from the server. */
  async getCart(): Promise<ApiResponse<CartResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.INDEX)
  },

  /** Add an item to the server-side cart. */
  async addItem(id: number, type: string, quantity: number = 1): Promise<ApiResponse<CartResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.ADD_ITEM, {
      method: 'POST',
      body: { id, type, quantity },
    })
  },

  /** Update an item's quantity. */
  async updateItem(id: number, type: string, quantity: number): Promise<ApiResponse<CartResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.UPDATE_ITEM, {
      method: 'PUT',
      body: { id, type, quantity },
    })
  },

  /** Remove an item from the cart. */
  async removeItem(id: number, type: string): Promise<ApiResponse<CartResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.REMOVE_ITEM, {
      method: 'DELETE',
      body: { id, type },
    })
  },

  /** Clear all cart items. */
  async clearCart(): Promise<ApiResponse<null>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.CLEAR, {
      method: 'DELETE',
    })
  },

  /** Validate a promo code against the given subtotal. */
  async validatePromo(code: string, subtotal: number): Promise<ApiResponse<PromoValidationResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.CART.VALIDATE_PROMO, {
      method: 'POST',
      body: { code, subtotal },
    })
  },
}
