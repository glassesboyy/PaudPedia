<script setup lang="ts">
/**
 * CheckoutOrderSummary — Read-only order review for checkout page.
 *
 * Displays items, subtotal, discount, and total in a compact summary.
 */
import type { CartItem } from '~~/types';

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
  course: 'bg-primary-50 text-primary-700 ring-1 ring-primary-200/50',
  webinar: 'bg-secondary-50 text-secondary-700 ring-1 ring-secondary-200/50',
  product: 'bg-success-50 text-success-700 ring-1 ring-success-200/50',
}

const typeIcons: Record<string, string> = {
  course: 'lucide:graduation-cap',
  webinar: 'lucide:video',
  product: 'lucide:package',
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
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-heading uppercase tracking-wider flex items-center gap-2">
          <Icon name="lucide:package" class="w-4 h-4 text-primary-500" />
          Ringkasan Pesanan
        </h3>
        <span class="text-xs font-medium text-muted bg-surface px-2.5 py-1 rounded-full border border-border-muted">
          {{ items.length }} item
        </span>
      </div>
    </div>

    <!-- Items -->
    <div class="divide-y divide-border-muted">
      <div
        v-for="item in items"
        :key="`${item.type}-${item.id}`"
        class="flex items-center gap-3.5 px-5 py-3.5"
      >
        <!-- Thumbnail -->
        <div class="w-14 h-14 rounded-xl overflow-hidden bg-surface-sunken flex-shrink-0 ring-1 ring-black/5">
          <img
            v-if="item.thumbnail"
            :src="item.thumbnail"
            :alt="item.name"
            class="w-full h-full object-cover"
            loading="lazy"
          />
          <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-sunken to-surface-muted">
            <Icon :name="typeIcons[item.type] || 'lucide:package'" class="w-5 h-5 text-muted/25" />
          </div>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-1.5 mb-1">
            <span
              :class="['inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide', typeColors[item.type] || 'bg-surface-muted text-body']"
            >
              <Icon :name="typeIcons[item.type] || 'lucide:package'" class="w-2.5 h-2.5" />
              {{ typeLabels[item.type] || item.type }}
            </span>
          </div>
          <p class="text-sm font-medium text-heading truncate">{{ item.name }}</p>
          <p v-if="item.quantity > 1" class="text-xs text-muted mt-0.5">{{ item.quantity }}x @ {{ formatPrice(item.price) }}</p>
        </div>

        <!-- Price -->
        <span class="text-sm font-semibold text-heading flex-shrink-0 tabular-nums">
          {{ item.price === 0 ? 'Gratis' : formatPrice(item.price * item.quantity) }}
        </span>
      </div>
    </div>

    <!-- Totals -->
    <div class="px-5 py-4 border-t border-border bg-surface-muted/30 space-y-2">
      <div class="flex items-center justify-between">
        <span class="text-sm text-body">Subtotal</span>
        <span class="text-sm font-medium text-heading tabular-nums">{{ formatPrice(subtotal) }}</span>
      </div>

      <div v-if="promoCode && discount > 0" class="flex items-center justify-between">
        <div class="flex items-center gap-1.5">
          <Icon name="lucide:badge-percent" class="w-3.5 h-3.5 text-success-600" />
          <span class="text-sm text-success-700">Diskon ({{ promoCode }})</span>
        </div>
        <span class="text-sm font-semibold text-success-700 tabular-nums">-{{ formatPrice(discount) }}</span>
      </div>

      <div class="flex items-center justify-between pt-2.5 border-t border-border-muted">
        <span class="text-base font-bold text-heading">Total</span>
        <span class="text-xl font-bold text-primary-600 tabular-nums">{{ formatPrice(total) }}</span>
      </div>
    </div>
  </div>
</template>
