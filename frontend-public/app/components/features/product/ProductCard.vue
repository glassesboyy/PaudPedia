<script setup lang="ts">
/**
 * ProductCard — Reusable product preview card.
 *
 * Displays thumbnail, title, category, file info, price.
 * Uses design system tokens exclusively.
 */
import type { Product } from '~~/types';

defineProps<{
  product: Product
}>()

function formatPrice(price: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}
</script>

<template>
  <NuxtLink
    :to="`/products/${product.slug}`"
    class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-medium hover:border-primary-200 hover:-translate-y-1 transition-all duration-300"
  >
    <!-- Thumbnail -->
    <div class="relative aspect-video overflow-hidden bg-surface-sunken">
      <img
        v-if="product.thumbnail_url"
        :src="product.thumbnail_url"
        :alt="product.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <Icon name="lucide:package" class="w-10 h-10 text-muted" />
      </div>
      <!-- File type badge -->
      <span
        v-if="(product as any).file_info?.type"
        class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-medium bg-surface/90 text-heading backdrop-blur-sm"
      >
        {{ (product as any).file_info.type }}
      </span>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-4">
      <!-- Category -->
      <p v-if="product.category" class="text-xs text-primary-600 font-medium mb-1.5">
        {{ product.category.name }}
      </p>

      <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors">
        {{ product.title }}
      </h3>

      <p v-if="product.description" class="text-xs text-body line-clamp-2 mb-3">
        {{ product.description }}
      </p>

      <div class="mt-auto pt-3 border-t border-border-muted flex items-center justify-between">
        <!-- File size -->
        <span
          v-if="(product as any).file_info?.size_formatted"
          class="text-xs text-muted flex items-center gap-1"
        >
          <Icon name="lucide:file" class="w-3.5 h-3.5" />
          {{ (product as any).file_info.size_formatted }}
        </span>
        <span v-else />

        <!-- Price -->
        <div class="text-right">
          <span
            v-if="product.original_price && product.original_price > product.price"
            class="block text-xs text-muted line-through"
          >
            {{ formatPrice(product.original_price) }}
          </span>
          <span class="text-sm font-bold text-primary-600">
            {{ product.price === 0 ? 'Gratis' : formatPrice(product.price) }}
          </span>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>
