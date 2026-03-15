<script setup lang="ts">
/**
 * CheckoutPromoInput — Promo code input with apply/clear for checkout page.
 *
 * Standalone promo component for the checkout flow.
 * Uses design system tokens exclusively.
 */

defineProps<{
  promoCode: string | null
  discount: number
  isValidatingPromo: boolean
  promoError: string | null
}>()

const emit = defineEmits<{
  applyPromo: [code: string]
  clearPromo: []
}>()

const promoInput = ref('')

function handleApply() {
  if (promoInput.value.trim()) {
    emit('applyPromo', promoInput.value.trim())
  }
}

function handleClear() {
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
  <div class="rounded-xl border border-border bg-surface p-5">
    <h3 class="text-sm font-semibold text-heading mb-3">Kode Promo</h3>

    <template v-if="promoCode">
      <!-- Active promo -->
      <div class="flex items-center justify-between gap-2 p-3 rounded-lg bg-success-50 border border-success-200">
        <div class="flex items-center gap-2">
          <Icon name="lucide:ticket" class="w-4 h-4 text-success-600" />
          <div>
            <span class="text-sm font-semibold text-success-700">{{ promoCode }}</span>
            <p v-if="discount > 0" class="text-xs text-success-600">Hemat {{ formatPrice(discount) }}</p>
          </div>
        </div>
        <button
          type="button"
          class="p-1 rounded-md text-success-600 hover:text-danger-600 hover:bg-danger-50 transition-colors"
          title="Hapus promo"
          @click="handleClear"
        >
          <Icon name="lucide:x" class="w-4 h-4" />
        </button>
      </div>
    </template>

    <template v-else>
      <!-- Promo input -->
      <div class="flex gap-2">
        <input
          v-model="promoInput"
          type="text"
          placeholder="Masukkan kode promo"
          class="flex-1 px-3 py-2 rounded-lg border border-border bg-surface text-sm text-heading placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 transition-colors"
          :disabled="isValidatingPromo"
          @keyup.enter="handleApply"
        />
        <UButton
          variant="outline"
          size="sm"
          :loading="isValidatingPromo"
          :disabled="!promoInput.trim() || isValidatingPromo"
          @click="handleApply"
        >
          Pakai
        </UButton>
      </div>
      <p v-if="promoError" class="text-xs text-danger-600 mt-1.5">{{ promoError }}</p>
    </template>
  </div>
</template>
