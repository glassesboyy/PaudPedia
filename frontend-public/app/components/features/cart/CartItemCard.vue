<script setup lang="ts">
/**
 * CartItemCard — Individual cart item display.
 *
 * Shows thumbnail, name, type badge, price, quantity controls, and remove button.
 * Emits events for quantity changes and removal.
 * Uses design system tokens exclusively.
 */
import type { CartItem } from '~~/types'

defineProps<{
  item: CartItem
}>()

const emit = defineEmits<{
  updateQuantity: [id: number, type: string, quantity: number]
  remove: [id: number, type: string]
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

const typeIcons: Record<string, string> = {
  course: 'lucide:graduation-cap',
  webinar: 'lucide:video',
  product: 'lucide:package',
}

const typeRoutes: Record<string, string> = {
  course: '/courses/',
  webinar: '/webinars/',
  product: '/products/',
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
  <div class="flex gap-4 p-4 rounded-xl border border-border bg-surface hover:border-primary-100 transition-colors">
    <!-- Thumbnail -->
    <NuxtLink :to="typeRoutes[item.type] + item.slug" class="flex-shrink-0">
      <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden bg-surface-sunken">
        <img
          v-if="item.thumbnail"
          :src="item.thumbnail"
          :alt="item.name"
          class="w-full h-full object-cover"
          loading="lazy"
        />
        <div v-else class="w-full h-full flex items-center justify-center">
          <Icon :name="typeIcons[item.type] || 'lucide:package'" class="w-8 h-8 text-muted/30" />
        </div>
      </div>
    </NuxtLink>

    <!-- Info -->
    <div class="flex-1 min-w-0 flex flex-col">
      <!-- Type badge + name -->
      <div class="flex items-start gap-2 mb-1">
        <span
          :class="['inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide flex-shrink-0', typeColors[item.type] || 'bg-surface-muted text-body']"
        >
          <Icon :name="typeIcons[item.type] || 'lucide:package'" class="w-3 h-3" />
          {{ typeLabels[item.type] || item.type }}
        </span>
      </div>

      <NuxtLink
        :to="typeRoutes[item.type] + item.slug"
        class="text-sm font-semibold text-heading hover:text-primary-600 transition-colors line-clamp-2 leading-snug mb-2"
      >
        {{ item.name }}
      </NuxtLink>

      <!-- Bottom row: quantity + price -->
      <div class="mt-auto flex items-center justify-between gap-3">
        <!-- Quantity controls (only for product type) -->
        <div v-if="item.type === 'product'" class="flex items-center gap-1">
          <button
            type="button"
            class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-muted hover:text-foreground hover:border-primary-300 transition-colors"
            :disabled="item.quantity <= 1"
            :class="item.quantity <= 1 && 'opacity-40 cursor-not-allowed'"
            @click="emit('updateQuantity', item.id, item.type, item.quantity - 1)"
          >
            <Icon name="lucide:minus" class="w-3.5 h-3.5" />
          </button>
          <span class="w-8 text-center text-sm font-medium text-heading">{{ item.quantity }}</span>
          <button
            type="button"
            class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-muted hover:text-foreground hover:border-primary-300 transition-colors"
            @click="emit('updateQuantity', item.id, item.type, item.quantity + 1)"
          >
            <Icon name="lucide:plus" class="w-3.5 h-3.5" />
          </button>
        </div>
        <div v-else class="text-xs text-muted">
          Qty: 1
        </div>

        <!-- Price -->
        <span class="text-sm font-bold text-primary-600 flex-shrink-0">
          {{ item.price === 0 ? 'Gratis' : formatPrice(item.price * item.quantity) }}
        </span>
      </div>
    </div>

    <!-- Remove button -->
    <button
      type="button"
      class="self-start p-1.5 rounded-lg text-muted hover:text-danger-600 hover:bg-danger-50 transition-colors flex-shrink-0"
      title="Hapus item"
      @click="emit('remove', item.id, item.type)"
    >
      <Icon name="lucide:trash-2" class="w-4 h-4" />
    </button>
  </div>
</template>
