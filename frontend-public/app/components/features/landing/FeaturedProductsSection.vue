<script setup lang="ts">
/**
 * FeaturedProductsSection — Showcases featured products on landing page.
 *
 * Receives product data via props from the parent landing page.
 * Uses design system tokens exclusively.
 */
import type { Product } from '~~/types';

defineProps<{
  products: Product[]
}>()
</script>

<template>
  <SectionWrapper
    title="Produk Edukasi"
    subtitle="Temukan buku, modul, dan media pembelajaran untuk mendukung kegiatan belajar."
    bg="bg-surface-muted"
  >
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>

    <template v-if="products.length" #footer>
      <NuxtLink to="/products">
        <UButton variant="outline" size="md">
          Lihat Semua Produk
          <Icon name="lucide:arrow-right" class="w-4 h-4 ml-1.5" />
        </UButton>
      </NuxtLink>
    </template>
  </SectionWrapper>
</template>
