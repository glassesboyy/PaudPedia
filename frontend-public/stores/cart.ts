/**
 * Cart Store
 *
 * Server-backed cart state. The Pinia store acts as a local cache that
 * syncs with the backend API. Cart is empty when the user is not authenticated.
 */
import { defineStore } from 'pinia'
import type { CartItem } from '~~/types'

interface CartState {
  items: CartItem[]
  promoCode: string | null
  discount: number
  /** True while the initial cart fetch is in progress. */
  isLoading: boolean
  /** True while a cart mutation (add/remove/update) is in progress. */
  isMutating: boolean
  /** True once the initial cart fetch has completed (even if cart is empty). */
  hasFetched: boolean
}

export const useCartStore = defineStore('cart', {
  state: (): CartState => ({
    items: [],
    promoCode: null,
    discount: 0,
    isLoading: false,
    isMutating: false,
    hasFetched: false,
  }),

  getters: {
    itemCount: (state): number =>
      state.items.reduce((sum, item) => sum + item.quantity, 0),

    subtotal: (state): number =>
      state.items.reduce((sum, item) => sum + item.price * item.quantity, 0),

    total(): number {
      return Math.max(0, this.subtotal - this.discount)
    },

    isEmpty: (state): boolean => state.items.length === 0,

    hasItem: (state) => {
      return (itemId: number, itemType: string): boolean =>
        state.items.some((i) => i.id === itemId && i.type === itemType)
    },
  },

  actions: {
    /** Sync local state with the server response. */
    syncFromServer(data: { items: CartItem[]; subtotal: number }) {
      this.items = data.items
    },

    /** Reset all local cart state. */
    resetLocal() {
      this.items = []
      this.promoCode = null
      this.discount = 0
      this.hasFetched = false
    },

    setPromo(code: string | null) {
      this.promoCode = code
    },

    setDiscount(amount: number) {
      this.discount = amount
    },
  },
})
