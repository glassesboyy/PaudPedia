<script setup lang="ts">
/**
 * CartItemCard — Individual cart item display.
 *
 * Shows thumbnail, name, type badge, price, quantity controls, and remove button.
 * Emits events for quantity changes and removal.
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
  course: 'bg-primary-50 text-primary-700 ring-1 ring-primary-200/50',
  webinar: 'bg-secondary-50 text-secondary-700 ring-1 ring-secondary-200/50',
  product: 'bg-success-50 text-success-700 ring-1 ring-success-200/50',
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
  <div class="group flex gap-4 p-4 sm:p-5 rounded-2xl border border-border bg-surface hover:border-primary-200 hover:shadow-sm transition-all duration-200">
    <!-- Thumbnail -->
    <NuxtLink :to="typeRoutes[item.type] + item.slug" class="flex-shrink-0">
      <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-surface-sunken ring-1 ring-black/5">
        <img
          v-if="item.thumbnail"
          :src="item.thumbnail"
          :alt="item.name"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
          loading="lazy"
        />
        <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-surface-sunken to-surface-muted">
          <Icon :name="typeIcons[item.type] || 'lucide:package'" class="w-8 h-8 text-muted/25" />
        </div>
      </div>
    </NuxtLink>

    <!-- Info -->
    <div class="flex-1 min-w-0 flex flex-col">
      <!-- Type badge -->
      <div class="mb-1.5">
        <span
          :class="['inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide', typeColors[item.type] || 'bg-surface-muted text-body']"
        >
          <Icon :name="typeIcons[item.type] || 'lucide:package'" class="w-3 h-3" />
          {{ typeLabels[item.type] || item.type }}
        </span>
      </div>

      <!-- Name -->
      <NuxtLink
        :to="typeRoutes[item.type] + item.slug"
        class="text-sm font-semibold text-heading hover:text-primary-600 transition-colors line-clamp-2 leading-snug mb-auto"
      >
        {{ item.name }}
      </NuxtLink>

      <!-- Bottom row: item info + price -->
      <div class="mt-3 flex items-center justify-between gap-3">
        <div class="text-xs text-muted font-medium">
          1 item
        </div>

        <!-- Price -->
        <span class="text-sm font-bold text-primary-600 flex-shrink-0 tabular-nums">
          {{ item.price === 0 ? 'Gratis' : formatPrice(item.price) }}
        </span>
      </div>
    </div>

    <!-- Remove button -->
    <button
      type="button"
      class="self-start p-2 rounded-lg text-muted/50 hover:text-danger-600 hover:bg-danger-50 transition-all duration-200 flex-shrink-0 opacity-0 group-hover:opacity-100 focus:opacity-100"
      title="Hapus item"
      @click="emit('remove', item.id, item.type)"
    >
      <Icon name="lucide:trash-2" class="w-4 h-4" />
    </button>
  </div>
</template>
