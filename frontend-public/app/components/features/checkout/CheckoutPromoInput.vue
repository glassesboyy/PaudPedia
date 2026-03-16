<script setup lang="ts">
/**
 * CheckoutPromoInput — Promo code input with apply/clear for checkout page.
 *
 * Standalone promo component for the checkout flow.
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
  <div class="rounded-2xl border border-border bg-surface shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-border-muted bg-surface-muted/40">
      <h3 class="text-sm font-bold text-heading uppercase tracking-wider flex items-center gap-2">
        <Icon name="lucide:ticket" class="w-4 h-4 text-primary-500" />
        Kode Promo
      </h3>
    </div>

    <div class="p-5">
      <template v-if="promoCode">
        <!-- Active promo -->
        <div class="flex items-center justify-between gap-2 p-3 rounded-xl bg-success-50/70 border border-success-200/50">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-success-100 flex items-center justify-center">
              <Icon name="lucide:badge-percent" class="w-4 h-4 text-success-600" />
            </div>
            <div>
              <span class="text-sm font-bold text-success-700">{{ promoCode }}</span>
              <p v-if="discount > 0" class="text-xs text-success-600">Hemat {{ formatPrice(discount) }}</p>
            </div>
          </div>
          <button
            type="button"
            class="p-1.5 rounded-lg text-success-400 hover:text-danger-600 hover:bg-danger-50 transition-all"
            title="Hapus promo"
            @click="handleClear"
          >
            <Icon name="lucide:x" class="w-4 h-4" />
          </button>
        </div>
      </template>

      <template v-else>
        <!-- Promo input -->
        <label class="text-xs font-medium text-muted mb-2 block">Punya kode promo? Masukkan di bawah</label>
        <div class="flex gap-2">
          <input
            v-model="promoInput"
            type="text"
            placeholder="Masukkan kode promo"
            class="flex-1 px-3 py-2.5 rounded-lg border border-border bg-surface text-sm text-heading placeholder:text-muted/60 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all uppercase"
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
            <Icon name="lucide:tag" class="w-3.5 h-3.5 mr-1" />
            Pakai
          </UButton>
        </div>
        <p v-if="promoError" class="flex items-center gap-1 text-xs text-danger-600 mt-2">
          <Icon name="lucide:alert-circle" class="w-3 h-3 flex-shrink-0" />
          {{ promoError }}
        </p>
      </template>
    </div>
  </div>
</template>
