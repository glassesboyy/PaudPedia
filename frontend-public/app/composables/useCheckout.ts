/**
 * useCheckout Composable
 *
 * Checkout flow orchestration with Midtrans Snap integration.
 */
import { useCartStore } from '~~/stores/cart'
import type { CartItem, CheckoutOrder } from '~~/types'

declare global {
  interface Window {
    snap?: {
      pay: (token: string, options: {
        onSuccess?: (result: unknown) => void
        onPending?: (result: unknown) => void
        onError?: (result: unknown) => void
        onClose?: () => void
      }) => void
    }
  }
}

export function useCheckout() {
  const cartStore = useCartStore()
  const toast = useToast()

  const isProcessing = ref(false)
  const checkoutError = ref<string | null>(null)
  const currentOrder = ref<CheckoutOrder | null>(null)

  async function processCheckout() {
    if (cartStore.isEmpty) {
      toast.warning('Keranjang kosong')
      return
    }

    isProcessing.value = true
    checkoutError.value = null

    try {
      const { checkoutService } = await import('~~/services')
      const payload = {
        items: cartStore.items.map((item: CartItem) => ({
          id: item.id,
          type: item.type,
          quantity: item.quantity,
        })),
        promo_code: cartStore.promoCode,
      }

      const res = await checkoutService.create(payload)

      if (res.success && res.data) {
        currentOrder.value = res.data.order
        const snapToken = res.data.snap_token

        openMidtransSnap(snapToken)
      } else {
        checkoutError.value = res.message || 'Gagal membuat pesanan'
        toast.error(checkoutError.value!)
      }
    } catch (err: unknown) {
      const fetchErr = err as { data?: { message?: string } }
      checkoutError.value = fetchErr?.data?.message || 'Terjadi kesalahan saat checkout'
      toast.error(checkoutError.value!)
    } finally {
      isProcessing.value = false
    }
  }

  function openMidtransSnap(token: string) {
    if (!window.snap) {
      checkoutError.value = 'Payment gateway belum siap. Silakan muat ulang halaman.'
      toast.error(checkoutError.value!)
      return
    }

    window.snap.pay(token, {
      onSuccess: () => {
        cartStore.clearCart()
        toast.success('Pembayaran berhasil!')
        navigateTo('/account/orders')
      },
      onPending: () => {
        cartStore.clearCart()
        toast.info('Menunggu pembayaran...')
        navigateTo('/account/orders')
      },
      onError: () => {
        toast.error('Pembayaran gagal. Silakan coba lagi.')
      },
      onClose: () => {
        toast.warning('Pembayaran dibatalkan')
      },
    })
  }

  return {
    isProcessing,
    checkoutError,
    currentOrder,
    processCheckout,
  }
}
