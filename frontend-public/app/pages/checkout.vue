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
  isLoading,
  hasFetched,
  promoCode,
  isValidatingPromo,
  promoError,
  applyPromo,
  clearPromo,
  fetchCart,
} = useCart()

const { isProcessing, checkoutError, processCheckout } = useCheckout()

// Wait for cart to be fetched before checking isEmpty
onMounted(async () => {
  if (!hasFetched.value) {
    await fetchCart()
  }
  if (isEmpty.value) {
    navigateTo('/cart')
  }
})

async function handlePayment() {
  await processCheckout()
}

function formatPrice(price: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}
</script>

<template>
  <div class="bg-gradient-to-b from-surface via-surface to-primary-50/10 min-h-[60vh]">
    <div class="container py-8 sm:py-12"> 
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-muted mb-8">
        <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
        <Icon name="lucide:chevron-right" class="w-3 h-3" />
        <NuxtLink to="/cart" class="hover:text-primary-600 transition-colors">Keranjang</NuxtLink>
        <Icon name="lucide:chevron-right" class="w-3 h-3" />
        <span class="text-primary-600 font-medium">Checkout</span>
      </nav>

      <!-- Page header with step indicator -->
      <div class="flex items-center justify-between mb-8 pb-6 border-b border-border/50">
        <div class="flex items-center gap-4">
          <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center flex-shrink-0 shadow-sm">
            <Icon name="lucide:credit-card" class="w-5 h-5 text-white" />
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-heading tracking-tight">Checkout</h1>
            <p class="text-sm text-muted mt-0.5">Periksa pesanan Anda sebelum melakukan pembayaran</p>
          </div>
        </div>

        <!-- Step indicator (desktop) -->
        <div class="hidden sm:flex items-center gap-2 text-xs">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-success-50 text-success-700 font-medium">
            <Icon name="lucide:check" class="w-3.5 h-3.5" />
            Keranjang
          </span>
          <Icon name="lucide:chevron-right" class="w-3.5 h-3.5 text-border" />
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary-500 text-white font-semibold">
            <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-[10px]">2</span>
            Checkout
          </span>
          <Icon name="lucide:chevron-right" class="w-3.5 h-3.5 text-border" />
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-muted text-muted font-medium">
            <span class="w-4 h-4 rounded-full bg-border/50 flex items-center justify-center text-[10px]">3</span>
            Pembayaran
          </span>
        </div>
      </div>

      <!-- Content -->
      <div v-if="isLoading && !hasFetched" class="flex flex-col items-center justify-center py-20 gap-4">
        <div class="w-10 h-10 border-3 border-primary-200 border-t-primary-500 rounded-full animate-spin" />
        <p class="text-sm text-muted">Memuat data pesanan...</p>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8">
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
          <div class="rounded-2xl border border-border bg-surface shadow-sm sticky top-24 overflow-hidden">
            <!-- Header -->
            <div class="px-5 py-4 bg-surface-muted/40 border-b border-border-muted">
              <h3 class="text-sm font-bold text-heading uppercase tracking-wider flex items-center gap-2">
                <Icon name="lucide:wallet" class="w-4 h-4 text-primary-500" />
                Pembayaran
              </h3>
            </div>

            <div class="p-5">
              <!-- Total highlight -->
              <div class="flex items-center justify-between p-3 rounded-xl bg-primary-50/50 border border-primary-100 mb-5">
                <span class="text-sm font-medium text-body">Total Bayar</span>
                <span class="text-xl font-bold text-primary-600 tabular-nums">{{ formatPrice(total) }}</span>
              </div>

              <!-- Payment info -->
              <div class="space-y-2.5 mb-5">
                <div class="flex items-start gap-2.5 p-3 rounded-lg bg-surface-muted/50">
                  <Icon name="lucide:shield-check" class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5" />
                  <p class="text-xs text-body leading-relaxed">
                    Pembayaran aman via <strong class="text-heading">Midtrans</strong>. Pilih metode pembayaran setelah klik tombol di bawah.
                  </p>
                </div>

                <div class="flex items-start gap-2.5 p-3 rounded-lg bg-surface-muted/50">
                  <Icon name="lucide:zap" class="w-4 h-4 text-warning-500 flex-shrink-0 mt-0.5" />
                  <p class="text-xs text-muted leading-relaxed">
                    Akses konten otomatis diberikan setelah pembayaran berhasil.
                  </p>
                </div>
              </div>

              <!-- Error display -->
              <div
                v-if="checkoutError"
                class="flex items-start gap-2 p-3 rounded-lg bg-danger-50 border border-danger-200 mb-4"
              >
                <Icon name="lucide:alert-circle" class="w-4 h-4 text-danger-500 flex-shrink-0 mt-0.5" />
                <p class="text-xs text-danger-700">{{ checkoutError }}</p>
              </div>

              <!-- Pay button -->
              <UButton
                variant="primary"
                size="lg"
                block
                :loading="isProcessing"
                :disabled="isEmpty || isProcessing"
                @click="handlePayment"
              >
                <Icon v-if="!isProcessing" name="lucide:lock" class="w-4 h-4 mr-2" />
                {{ isProcessing ? 'Memproses...' : 'Bayar Sekarang' }}
              </UButton>

              <!-- Back to cart -->
              <div class="text-center mt-3">
                <NuxtLink
                  to="/cart"
                  class="inline-flex items-center gap-1.5 text-xs text-muted hover:text-primary-600 transition-colors group"
                >
                  <Icon name="lucide:arrow-left" class="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" />
                  Kembali ke keranjang
                </NuxtLink>
              </div>

              <!-- Trust badges -->
              <div class="flex items-center justify-center gap-4 mt-5 pt-4 border-t border-border-muted">
                <div class="flex items-center gap-1.5 text-muted">
                  <Icon name="lucide:lock" class="w-3.5 h-3.5" />
                  <span class="text-[11px]">SSL Aman</span>
                </div>
                <div class="flex items-center gap-1.5 text-muted">
                  <Icon name="lucide:shield-check" class="w-3.5 h-3.5" />
                  <span class="text-[11px]">Midtrans</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
