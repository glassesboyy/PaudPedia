<script setup lang="ts">
/**
 * ProductCard — Reusable product preview card.
 *
 * Displays thumbnail, title, category, file info, price with discount.
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
    <div class="relative aspect-[4/3] overflow-hidden bg-surface-sunken">
      <img
        v-if="product.thumbnail_url"
        :src="product.thumbnail_url"
        :alt="product.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />
      <div v-else class="w-full h-full flex items-center justify-center bg-primary-50/50">
        <Icon name="lucide:package" class="w-12 h-12 text-primary-300" />
      </div>

      <!-- File type badge -->
      <span
        v-if="product.file_info?.type"
        class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide bg-surface/90 text-heading backdrop-blur-sm shadow-sm"
      >
        {{ product.file_info.type }}
      </span>

      <!-- Discount badge -->
      <span
        v-if="product.has_discount && product.discount_percentage"
        class="absolute top-3 right-3 px-2 py-0.5 rounded-lg text-[11px] font-bold bg-danger-500 text-white shadow-sm"
      >
        -{{ product.discount_percentage }}%
      </span>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-4">
      <!-- Category -->
      <p v-if="product.category" class="text-[11px] text-primary-600 font-semibold uppercase tracking-wide mb-1.5">
        {{ product.category.name }}
      </p>

      <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors leading-snug">
        {{ product.title }}
      </h3>

      <p v-if="product.description" class="text-xs text-body/70 line-clamp-2 mb-3 leading-relaxed">
        {{ product.description }}
      </p>

      <!-- Footer -->
      <div class="mt-auto pt-3 border-t border-border-muted flex items-center justify-between gap-2">
        <!-- File info -->
        <div class="flex items-center gap-2 text-xs text-muted min-w-0">
          <span
            v-if="product.file_info?.size_formatted"
            class="inline-flex items-center gap-1 shrink-0"
          >
            <Icon name="lucide:hard-drive" class="w-3.5 h-3.5" />
            {{ product.file_info.size_formatted }}
          </span>
        </div>

        <!-- Price -->
        <div class="text-right shrink-0">
          <span
            v-if="product.has_discount && product.original_price"
            class="block text-[11px] text-muted line-through"
          >
            {{ formatPrice(product.original_price) }}
          </span>
          <span class="text-sm font-bold" :class="product.price === 0 ? 'text-success-600' : 'text-primary-600'">
            {{ product.price === 0 ? 'Gratis' : formatPrice(product.price) }}
          </span>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>
