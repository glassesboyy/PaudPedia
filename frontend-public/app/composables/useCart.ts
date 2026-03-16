/**
 * useCart Composable
 *
 * Wraps cart store with convenience methods, promo validation, and toast feedback.
 */
import { useAuthStore } from '~~/stores/auth'
import { useCartStore } from '~~/stores/cart'
import type { CartItem } from '~~/types'

export function useCart() {
  const cartStore = useCartStore()
  const authStore = useAuthStore()
  const toast = useToast()
  const isValidatingPromo = ref(false)
  const promoError = ref<string | null>(null)

  function addToCart(item: Omit<CartItem, 'quantity'>) {
    // Redirect to login if not authenticated
    if (!authStore.isAuthenticated) {
      toast.warning('Silakan masuk terlebih dahulu untuk menambahkan item ke keranjang')
      navigateTo('/auth/login')
      return
    }

    // Ensure cart belongs to the current user
    if (authStore.user?.id) {
      cartStore.ensureOwner(authStore.user.id)
    }

    // Prevent duplicate for course/webinar (quantity always 1)
    if (
      (item.type === 'course' || item.type === 'webinar')
      && cartStore.hasItem(item.id, item.type)
    ) {
      toast.warning(`${item.name} sudah ada di keranjang`)
      return
    }
    cartStore.addItem(item)
    toast.success(`${item.name} ditambahkan ke keranjang`)
  }

  function removeFromCart(itemId: number, itemType: string) {
    cartStore.removeItem(itemId, itemType)
    toast.info('Item dihapus dari keranjang')
    // Reset promo if cart changes
    if (cartStore.promoCode) {
      clearPromo()
    }
  }

  function updateQuantity(itemId: number, itemType: string, quantity: number) {
    cartStore.updateQuantity(itemId, itemType, quantity)
    // Reset promo if cart changes
    if (cartStore.promoCode) {
      clearPromo()
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

  return {
    items: computed(() => cartStore.items),
    total: computed(() => cartStore.total),
    subtotal: computed(() => cartStore.subtotal),
    itemCount: computed(() => cartStore.itemCount),
    isEmpty: computed(() => cartStore.isEmpty),
    promoCode: computed(() => cartStore.promoCode),
    discount: computed(() => cartStore.discount),
    isValidatingPromo,
    promoError,
    hasItem: (id: number, type: string) => cartStore.hasItem(id, type),
    addToCart,
    removeFromCart,
    updateQuantity,
    applyPromo,
    clearPromo,
    clearCart: cartStore.clearCart,
  }
}
