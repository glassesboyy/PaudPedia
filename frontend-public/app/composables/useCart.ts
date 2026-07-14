/**
 * useCart Composable
 *
 * Server-backed cart operations with toast feedback and optimistic updates.
 * Fetches cart from the API on first use when authenticated.
 * Unauthenticated users always see an empty cart.
 */
import { useAuthStore } from '~~/stores/auth'
import { useCartStore } from '~~/stores/cart'
import type { CartItem } from '~~/types'

/** Deduplication — only one fetch in-flight at a time. */
let _fetchPromise: Promise<void> | null = null

export function useCart() {
  const cartStore = useCartStore()
  const authStore = useAuthStore()
  const toast = useToast()
  const isValidatingPromo = ref(false)
  const promoError = ref<string | null>(null)
  /** Track which item is being added (for per-button loading indicator). */
  const addingItemKey = ref<string | null>(null)

  // Fetch cart from server on first use when authenticated
  if (authStore.isAuthenticated && !cartStore.hasFetched && !cartStore.isLoading) {
    fetchCart()
  }

  /**
   * Fetch the full cart from the server and sync local state.
   */
  async function fetchCart(): Promise<void> {
    if (!authStore.isAuthenticated) {
      cartStore.resetLocal()
      return
    }
    if (_fetchPromise) return _fetchPromise
    cartStore.isLoading = true
    _fetchPromise = (async () => {
      try {
        const { cartService } = await import('~~/services')
        const res = await cartService.getCart()
        if (res.success && res.data) {
          cartStore.syncFromServer(res.data)
        }
      } catch {
        // Silently fail — cart will appear empty
      } finally {
        cartStore.isLoading = false
        cartStore.hasFetched = true
        _fetchPromise = null
      }
    })()
    return _fetchPromise
  }

  async function addToCart(item: Omit<CartItem, 'quantity'>) {
    if (!authStore.isAuthenticated) {
      toast.warning('Silakan masuk terlebih dahulu untuk menambahkan item ke keranjang')
      navigateTo('/auth/login')
      return
    }

    // Optimistic: check local duplicate for all items (course/webinar/product)
    if (
      (item.type === 'course' || item.type === 'webinar' || item.type === 'product')
      && cartStore.hasItem(item.id, item.type)
    ) {
      toast.warning(`${item.name} sudah ada di keranjang`)
      return
    }

    const itemKey = `${item.type}-${item.id}`
    addingItemKey.value = itemKey
    cartStore.isMutating = true
    try {
      const { cartService } = await import('~~/services')
      const res = await cartService.addItem(item.id, item.type)
      if (res.success && res.data) {
        cartStore.syncFromServer(res.data)
        toast.success(`${item.name} ditambahkan ke keranjang`)
      } else {
        toast.error(res.message || 'Gagal menambahkan item')
      }
    } catch (err: unknown) {
      const fetchErr = err as { data?: { message?: string } }
      toast.error(fetchErr?.data?.message || 'Gagal menambahkan item ke keranjang')
    } finally {
      addingItemKey.value = null
      cartStore.isMutating = false
    }
  }

  async function removeFromCart(itemId: number, itemType: string) {
    // Optimistic: remove from local state immediately
    const previousItems = [...cartStore.items]
    cartStore.items = cartStore.items.filter(
      (i) => !(i.id === itemId && i.type === itemType),
    )
    toast.info('Item dihapus dari keranjang')

    // Reset promo if active
    if (cartStore.promoCode) {
      clearPromo()
    }

    cartStore.isMutating = true
    try {
      const { cartService } = await import('~~/services')
      const res = await cartService.removeItem(itemId, itemType)
      if (res.success && res.data) {
        cartStore.syncFromServer(res.data)
      }
    } catch {
      // Revert on failure
      cartStore.items = previousItems
      toast.error('Gagal menghapus item dari keranjang')
    } finally {
      cartStore.isMutating = false
    }
  }

  async function updateQuantity(itemId: number, itemType: string, quantity: number) {
    // Optimistic: update local state immediately
    const item = cartStore.items.find(
      (i) => i.id === itemId && i.type === itemType,
    )
    if (!item) return

    const previousQuantity = item.quantity
    if (quantity <= 0) {
      return removeFromCart(itemId, itemType)
    }
    item.quantity = quantity

    // Reset promo if active
    if (cartStore.promoCode) {
      clearPromo()
    }

    cartStore.isMutating = true
    try {
      const { cartService } = await import('~~/services')
      const res = await cartService.updateItem(itemId, itemType, quantity)
      if (res.success && res.data) {
        cartStore.syncFromServer(res.data)
      }
    } catch {
      // Revert on failure
      item.quantity = previousQuantity
      toast.error('Gagal memperbarui keranjang')
    } finally {
      cartStore.isMutating = false
    }
  }

  async function applyPromo(code: string) {
    if (!code.trim()) return
    isValidatingPromo.value = true
    promoError.value = null
    try {
      const { cartService } = await import('~~/services')
      const res = await cartService.validatePromo(code, cartStore.subtotal)
      if (res.success && res.data?.valid) {
        cartStore.setPromo(code.toUpperCase())
        cartStore.setDiscount(res.data.discount)
        toast.success('Kode promo berhasil diterapkan!')
      } else {
        const msg = res.data?.message || res.message || 'Kode promo tidak valid'
        promoError.value = msg
        toast.error(msg)
      }
    } catch {
      promoError.value = 'Gagal memvalidasi kode promo'
      toast.error('Gagal memvalidasi kode promo')
    } finally {
      isValidatingPromo.value = false
    }
  }

  function clearPromo() {
    cartStore.setPromo(null)
    cartStore.setDiscount(0)
    promoError.value = null
  }

  async function clearCart() {
    try {
      const { cartService } = await import('~~/services')
      await cartService.clearCart()
    } catch {
      // Best-effort
    }
    cartStore.resetLocal()
  }

  /** Check if a specific item is currently being added. */
  function isAddingItem(id: number, type: string): boolean {
    return addingItemKey.value === `${type}-${id}`
  }

  return {
    items: computed(() => cartStore.items),
    total: computed(() => cartStore.total),
    subtotal: computed(() => cartStore.subtotal),
    itemCount: computed(() => cartStore.itemCount),
    isEmpty: computed(() => cartStore.isEmpty),
    promoCode: computed(() => cartStore.promoCode),
    discount: computed(() => cartStore.discount),
    isLoading: computed(() => cartStore.isLoading),
    isMutating: computed(() => cartStore.isMutating),
    hasFetched: computed(() => cartStore.hasFetched),
    addingItemKey: computed(() => addingItemKey.value),
    isValidatingPromo,
    promoError,
    hasItem: (id: number, type: string) => cartStore.hasItem(id, type),
    isAddingItem,
    addToCart,
    removeFromCart,
    updateQuantity,
    applyPromo,
    clearPromo,
    clearCart,
    fetchCart,
  }
}
