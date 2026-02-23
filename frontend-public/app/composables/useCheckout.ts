/**
 * useCheckout Composable
 *
 * Checkout flow orchestration.
 * Will be extended during feature development.
 */
export function useCheckout() {
  const isProcessing = ref(false)

  return {
    isProcessing,
  }
}
