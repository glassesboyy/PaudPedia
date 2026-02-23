/**
 * useCart Composable
 *
 * Wraps cart store with convenience methods and toast feedback.
 */
import type { CartItem } from '~/types'

export function useCart() {
  const cartStore = useCartStore()
  const toast = useToast()

  async function addToCart(item: Omit<CartItem, 'quantity'>) {
    try {
      cartStore.addItem(item)
      toast.success(`${item.name} ditambahkan ke keranjang`)
    } catch {
      toast.error('Gagal menambahkan ke keranjang')
    }
  }

  function removeFromCart(itemId: number, itemType: string) {
    cartStore.removeItem(itemId, itemType)
  }

  const total = computed(() => cartStore.total)
  const itemCount = computed(() => cartStore.itemCount)
  const isEmpty = computed(() => cartStore.isEmpty)

  return {
    items: computed(() => cartStore.items),
    total,
    itemCount,
    isEmpty,
    addToCart,
    removeFromCart,
    clearCart: cartStore.clearCart,
  }
}
