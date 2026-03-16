/**
 * Cart Store
 *
 * Client-side cart state. Persisted to localStorage.
 * Cart data is scoped per user — switching accounts clears stale data.
 */
import { defineStore } from 'pinia'
import type { CartItem } from '~~/types'

interface CartState {
  items: CartItem[]
  promoCode: string | null
  discount: number
  /** ID of the user who owns this cart. Used to prevent data leaking between accounts. */
  ownerId: number | null
}

export const useCartStore = defineStore('cart', {
  state: (): CartState => ({
    items: [],
    promoCode: null,
    discount: 0,
    ownerId: null,
  }),

  getters: {
    itemCount: (state): number => state.items.reduce((sum, item) => sum + item.quantity, 0),

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
    /**
     * Ensure the cart belongs to the given user.
     * If the stored ownerId differs, the cart is cleared first.
     */
    ensureOwner(userId: number) {
      if (this.ownerId !== userId) {
        this.items = []
        this.promoCode = null
        this.discount = 0
        this.ownerId = userId
      }
    },

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

    updateQuantity(itemId: number, itemType: string, quantity: number) {
      const item = this.items.find(
        (i) => i.id === itemId && i.type === itemType,
      )
      if (item) {
        if (quantity <= 0) {
          this.removeItem(itemId, itemType)
        } else {
          item.quantity = quantity
        }
      }
    },

    setPromo(code: string | null) {
      this.promoCode = code
    },

    setDiscount(amount: number) {
      this.discount = amount
    },

    clearCart() {
      this.items = []
      this.promoCode = null
      this.discount = 0
      this.ownerId = null
    },
  },

  persist: true,
})
