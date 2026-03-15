<script setup lang="ts">
/**
 * Checkout Page
 * Route: /checkout
 *
 * Reviews order, allows promo, and triggers Midtrans Snap payment.
 * Requires authentication. Redirects to cart if cart is empty.
 */
definePageMeta({
  layout: 'minimal',
  middleware: ['auth'],
})

useSeo({ title: 'Checkout' })

const {
  items,
  subtotal,
  total,
  discount,
  isEmpty,
  promoCode,
  isValidatingPromo,
  promoError,
  applyPromo,
  clearPromo,
} = useCart()

const { isProcessing, checkoutError, processCheckout } = useCheckout()

// Redirect to cart if empty
onMounted(() => {
  if (isEmpty.value) {
    navigateTo('/cart')
  }
})

async function handlePayment() {
  await processCheckout()
}
</script>

<template>
  <div class="bg-gradient-to-b from-surface to-primary-50/10 min-h-[60vh]">
    <div class="container py-8 sm:py-10 max-w-4xl">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-muted mb-6">
        <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
        <Icon name="lucide:chevron-right" class="w-3 h-3" />
        <NuxtLink to="/cart" class="hover:text-primary-600 transition-colors">Keranjang</NuxtLink>
        <Icon name="lucide:chevron-right" class="w-3 h-3" />
        <span class="text-primary-600 font-medium">Checkout</span>
      </nav>

      <!-- Page header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
          <Icon name="lucide:credit-card" class="w-5 h-5 text-primary-600" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-heading">Checkout</h1>
          <p class="text-sm text-muted">Periksa pesanan Anda sebelum melakukan pembayaran</p>
        </div>
      </div>

      <!-- Content -->
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8">
        <!-- Left: Order summary + Promo (3/5 width) -->
        <div class="lg:col-span-3 space-y-5">
          <!-- Order summary -->
          <CheckoutOrderSummary
            :items="items"
            :subtotal="subtotal"
            :discount="discount"
            :total="total"
            :promo-code="promoCode"
          />

          <!-- Promo input -->
          <CheckoutPromoInput
            :promo-code="promoCode"
            :discount="discount"
            :is-validating-promo="isValidatingPromo"
            :promo-error="promoError"
            @apply-promo="applyPromo"
            @clear-promo="clearPromo"
          />
        </div>

        <!-- Right: Payment action (2/5 width) -->
        <div class="lg:col-span-2">
          <div class="rounded-xl border border-border bg-surface p-5 sticky top-24">
            <h3 class="text-base font-bold text-heading mb-4">Pembayaran</h3>

            <!-- Payment info -->
            <div class="space-y-3 mb-5">
              <div class="flex items-center gap-2.5 p-3 rounded-lg bg-primary-50/50 border border-primary-100">
                <Icon name="lucide:shield-check" class="w-5 h-5 text-primary-600 flex-shrink-0" />
                <p class="text-xs text-body leading-relaxed">
                  Pembayaran diproses secara aman melalui <strong class="text-heading">Midtrans</strong>.
                  Anda dapat memilih metode pembayaran setelah mengklik tombol di bawah.
                </p>
              </div>

              <div class="flex items-center gap-2.5 p-3 rounded-lg bg-surface-muted border border-border-muted">
                <Icon name="lucide:info" class="w-5 h-5 text-muted flex-shrink-0" />
                <p class="text-xs text-muted leading-relaxed">
                  Akses konten akan diberikan secara otomatis setelah pembayaran berhasil.
                </p>
              </div>
            </div>

            <!-- Error display -->
            <UAlert
              v-if="checkoutError"
              variant="error"
              :title="checkoutError"
              dismissible
              class="mb-4"
            />

            <!-- Pay button -->
            <UButton
              variant="primary"
              size="lg"
              block
              :loading="isProcessing"
              :disabled="isEmpty || isProcessing"
              @click="handlePayment"
            >
              <Icon v-if="!isProcessing" name="lucide:wallet" class="w-4 h-4 mr-2" />
              {{ isProcessing ? 'Memproses...' : 'Bayar Sekarang' }}
            </UButton>

            <!-- Back to cart -->
            <div class="text-center mt-3">
              <NuxtLink
                to="/cart"
                class="text-xs text-muted hover:text-primary-600 transition-colors"
              >
                Kembali ke keranjang
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
