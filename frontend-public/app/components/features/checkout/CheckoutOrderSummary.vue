<script setup lang="ts">
/**
 * CheckoutOrderSummary — Read-only order review for checkout page.
 *
 * Displays items, subtotal, discount, and total in a compact summary.
 * Uses design system tokens exclusively.
 */
import type { CartItem } from '~~/types'

defineProps<{
  items: CartItem[]
  subtotal: number
  discount: number
  total: number
  promoCode: string | null
}>()

const typeLabels: Record<string, string> = {
  course: 'Kursus',
  webinar: 'Webinar',
  product: 'Produk',
}

const typeColors: Record<string, string> = {
  course: 'bg-primary-50 text-primary-700',
  webinar: 'bg-secondary-50 text-secondary-700',
  product: 'bg-success-50 text-success-700',
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
  <div class="rounded-xl border border-border bg-surface">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-border-muted">
      <h3 class="text-base font-bold text-heading">Ringkasan Pesanan</h3>
      <p class="text-xs text-muted">{{ items.length }} item</p>
    </div>

    <!-- Items -->
    <div class="divide-y divide-border-muted">
      <div
        v-for="item in items"
        :key="`${item.type}-${item.id}`"
        class="flex items-center gap-3 px-5 py-3"
      >
        <!-- Thumbnail -->
        <div class="w-12 h-12 rounded-lg overflow-hidden bg-surface-sunken flex-shrink-0">
          <img
            v-if="item.thumbnail"
            :src="item.thumbnail"
            :alt="item.name"
            class="w-full h-full object-cover"
            loading="lazy"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <Icon name="lucide:package" class="w-5 h-5 text-muted/30" />
          </div>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1.5 mb-0.5">
            <span
              :class="['px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide', typeColors[item.type] || 'bg-surface-muted text-body']"
            >
              {{ typeLabels[item.type] || item.type }}
            </span>
          </div>
          <p class="text-sm font-medium text-heading truncate">{{ item.name }}</p>
          <p v-if="item.quantity > 1" class="text-xs text-muted">x{{ item.quantity }}</p>
        </div>

        <!-- Price -->
        <span class="text-sm font-semibold text-heading flex-shrink-0">
          {{ item.price === 0 ? 'Gratis' : formatPrice(item.price * item.quantity) }}
        </span>
      </div>
    </div>

    <!-- Totals -->
    <div class="px-5 py-4 border-t border-border bg-surface-muted/30 rounded-b-xl space-y-2">
      <div class="flex items-center justify-between">
        <span class="text-sm text-body">Subtotal</span>
        <span class="text-sm font-medium text-heading">{{ formatPrice(subtotal) }}</span>
      </div>

      <div v-if="promoCode && discount > 0" class="flex items-center justify-between">
        <div class="flex items-center gap-1.5">
          <Icon name="lucide:ticket" class="w-3.5 h-3.5 text-success-600" />
          <span class="text-sm text-success-700">Diskon ({{ promoCode }})</span>
        </div>
        <span class="text-sm font-medium text-success-700">-{{ formatPrice(discount) }}</span>
      </div>

      <div class="flex items-center justify-between pt-2 border-t border-border-muted">
        <span class="text-base font-bold text-heading">Total</span>
        <span class="text-lg font-bold text-primary-600">{{ formatPrice(total) }}</span>
      </div>
    </div>
  </div>
</template>
