/**
 * Cart Store
 *
 * Client-side cart state. Persisted to localStorage.
 */
import { defineStore } from 'pinia'
import type { CartItem } from '~~/types'

interface CartState {
  items: CartItem[]
  promoCode: string | null
  discount: number
}

export const useCartStore = defineStore('cart', {
  state: (): CartState => ({
    items: [],
    promoCode: null,
    discount: 0,
  }),

  getters: {
    itemCount: (state): number => state.items.reduce((sum, item) => sum + item.quantity, 0),

    subtotal: (state): number =>
      state.items.reduce((sum, item) => sum + item.price * item.quantity, 0),

    total(): number {
      return this.subtotal - this.discount
    },

    isEmpty: (state): boolean => state.items.length === 0,
  },

  actions: {
    addItem(item: Omit<CartItem, 'quantity'>) {
      const existing = this.items.find(
        (i) => i.id === item.id && i.type === item.type,
      )
      if (existing) {
        existing.quantity++
      } else {
        this.items.push({ ...item, quantity: 1 })
      }
    },

    removeItem(itemId: number, itemType: string) {
      const index = this.items.findIndex(
        (i) => i.id === itemId && i.type === itemType,
      )
      if (index > -1) {
        this.items.splice(index, 1)
      }
    },

    clearCart() {
      this.items = []
      this.promoCode = null
      this.discount = 0
    },
  },

  persist: true,
})
