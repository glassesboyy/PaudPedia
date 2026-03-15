<script setup lang="ts">
/**
 * CartSummary — Cart summary sidebar component.
 *
 * Displays subtotal, promo code input, discount, total, and checkout button.
 * Uses design system tokens exclusively.
 */

defineProps<{
  subtotal: number
  discount: number
  total: number
  promoCode: string | null
  isValidatingPromo: boolean
  promoError: string | null
  isEmpty: boolean
}>()

const emit = defineEmits<{
  applyPromo: [code: string]
  clearPromo: []
  checkout: []
}>()

const promoInput = ref('')

const { isAuthenticated } = useAuth()

function handleApplyPromo() {
  if (promoInput.value.trim()) {
    emit('applyPromo', promoInput.value.trim())
  }
}

function handleClearPromo() {
  promoInput.value = ''
  emit('clearPromo')
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
  <div class="rounded-xl border border-border bg-surface p-5 sticky top-24">
    <h3 class="text-base font-bold text-heading mb-4">Ringkasan Pesanan</h3>

    <!-- Subtotal -->
    <div class="flex items-center justify-between py-2.5">
      <span class="text-sm text-body">Subtotal</span>
      <span class="text-sm font-medium text-heading">{{ formatPrice(subtotal) }}</span>
    </div>

    <!-- Promo code section -->
    <div class="py-3 border-t border-border-muted">
      <template v-if="promoCode">
        <!-- Active promo -->
        <div class="flex items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <Icon name="lucide:ticket" class="w-4 h-4 text-success-600" />
            <span class="text-sm font-medium text-success-700">{{ promoCode }}</span>
          </div>
          <button
            type="button"
            class="text-xs text-muted hover:text-danger-600 transition-colors"
            @click="handleClearPromo"
          >
            Hapus
          </button>
        </div>
      </template>

      <template v-else>
        <!-- Promo input -->
        <div class="flex gap-2">
          <input
            v-model="promoInput"
            type="text"
            placeholder="Kode promo"
            class="flex-1 px-3 py-2 rounded-lg border border-border bg-surface text-sm text-heading placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 transition-colors"
            :disabled="isValidatingPromo"
            @keyup.enter="handleApplyPromo"
          />
          <UButton
            variant="outline"
            size="sm"
            :loading="isValidatingPromo"
            :disabled="!promoInput.trim() || isValidatingPromo"
            @click="handleApplyPromo"
          >
            Pakai
          </UButton>
        </div>
        <p v-if="promoError" class="text-xs text-danger-600 mt-1.5">{{ promoError }}</p>
      </template>
    </div>

    <!-- Discount -->
    <div v-if="discount > 0" class="flex items-center justify-between py-2.5 border-t border-border-muted">
      <span class="text-sm text-success-700">Diskon</span>
      <span class="text-sm font-medium text-success-700">-{{ formatPrice(discount) }}</span>
    </div>

    <!-- Total -->
    <div class="flex items-center justify-between py-3 border-t border-border mt-1">
      <span class="text-base font-bold text-heading">Total</span>
      <span class="text-lg font-bold text-primary-600">{{ formatPrice(total) }}</span>
    </div>

    <!-- Checkout button -->
    <template v-if="isAuthenticated">
      <UButton
        variant="primary"
        size="lg"
        block
        :disabled="isEmpty"
        class="mt-4"
        @click="emit('checkout')"
      >
        <Icon name="lucide:shield-check" class="w-4 h-4 mr-2" />
        Lanjut ke Checkout
      </UButton>
    </template>
    <template v-else>
      <NuxtLink to="/auth/login" class="block mt-4">
        <UButton variant="primary" size="lg" block>
          <Icon name="lucide:log-in" class="w-4 h-4 mr-2" />
          Masuk untuk Checkout
        </UButton>
      </NuxtLink>
      <p class="text-[11px] text-muted text-center mt-2">Silakan masuk terlebih dahulu untuk melanjutkan</p>
    </template>

    <!-- Security note -->
    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-border-muted">
      <Icon name="lucide:lock" class="w-3.5 h-3.5 text-muted flex-shrink-0" />
      <p class="text-[11px] text-muted">Transaksi aman dengan enkripsi SSL</p>
    </div>
  </div>
</template>
