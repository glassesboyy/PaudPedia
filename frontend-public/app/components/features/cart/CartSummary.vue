<script setup lang="ts">
/**
 * CartSummary — Cart summary sidebar component.
 *
 * Displays subtotal, promo code input, discount, total, and checkout button.
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
  <div class="rounded-2xl border border-border bg-surface shadow-sm sticky top-24 overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 bg-surface-muted/40 border-b border-border-muted">
      <h3 class="text-sm font-bold text-heading uppercase tracking-wider flex items-center gap-2">
        <Icon name="lucide:receipt" class="w-4 h-4 text-primary-500" />
        Ringkasan Pesanan
      </h3>
    </div>

    <div class="p-5">
      <!-- Subtotal -->
      <div class="flex items-center justify-between py-2">
        <span class="text-sm text-body">Subtotal</span>
        <span class="text-sm font-medium text-heading tabular-nums">{{ formatPrice(subtotal) }}</span>
      </div>

      <!-- Promo code section -->
      <div class="py-3 border-t border-border-muted">
        <template v-if="promoCode">
          <!-- Active promo -->
          <div class="flex items-center justify-between gap-2 p-2.5 rounded-lg bg-success-50/70 border border-success-200/50">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-md bg-success-100 flex items-center justify-center">
                <Icon name="lucide:ticket" class="w-3.5 h-3.5 text-success-600" />
              </div>
              <span class="text-sm font-semibold text-success-700">{{ promoCode }}</span>
            </div>
            <button
              type="button"
              class="p-1 rounded-md text-muted hover:text-danger-600 hover:bg-danger-50 transition-all"
              title="Hapus kode promo"
              @click="handleClearPromo"
            >
              <Icon name="lucide:x" class="w-3.5 h-3.5" />
            </button>
          </div>
        </template>

        <template v-else>
          <!-- Promo input -->
          <label class="text-xs font-medium text-muted mb-1.5 block">Punya kode promo?</label>
          <div class="flex gap-2">
            <input
              v-model="promoInput"
              type="text"
              placeholder="Masukkan kode"
              class="flex-1 px-3 py-2 rounded-lg border border-border bg-surface text-sm text-heading placeholder:text-muted/60 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all uppercase"
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
          <p v-if="promoError" class="flex items-center gap-1 text-xs text-danger-600 mt-1.5">
            <Icon name="lucide:alert-circle" class="w-3 h-3 flex-shrink-0" />
            {{ promoError }}
          </p>
        </template>
      </div>

      <!-- Discount -->
      <div v-if="discount > 0" class="flex items-center justify-between py-2 border-t border-border-muted">
        <span class="text-sm text-success-700 flex items-center gap-1.5">
          <Icon name="lucide:badge-percent" class="w-3.5 h-3.5" />
          Diskon
        </span>
        <span class="text-sm font-semibold text-success-700 tabular-nums">-{{ formatPrice(discount) }}</span>
      </div>

      <!-- Total -->
      <div class="flex items-center justify-between py-3 border-t-2 border-border mt-1">
        <span class="text-base font-bold text-heading">Total</span>
        <span class="text-xl font-bold text-primary-600 tabular-nums">{{ formatPrice(total) }}</span>
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
          <Icon name="lucide:arrow-right" class="w-4 h-4 mr-2" />
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
        <p class="text-[11px] text-muted text-center mt-2">Silakan masuk terlebih dahulu</p>
      </template>

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
</template>
