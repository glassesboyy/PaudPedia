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
  <section class="relative w-full bg-surface-muted overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute top-[10%] right-[5%] w-48 h-48 rounded-2xl bg-success-50/30 rotate-12" />
    <div class="absolute bottom-[10%] left-[5%] w-40 h-40 rounded-2xl bg-primary-50/40 -rotate-6" />
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full border border-border-muted/20" />

    <div class="container py-14 sm:py-20 relative z-10">
      <!-- Section header with badge -->
      <div class="text-center mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-success-50 border border-success-100 mb-4">
          <Icon name="lucide:package" class="w-3.5 h-3.5 text-success-500" />
          <span class="text-xs font-semibold text-success-600 uppercase tracking-wider">Marketplace</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-heading">
          Produk Edukasi
        </h2>
        <p class="mt-3 text-base text-body max-w-2xl mx-auto">
          Temukan buku, modul, dan media pembelajaran untuk mendukung kegiatan belajar.
        </p>
      </div>

      <!-- Product cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
        />
      </div>

      <!-- Footer -->
      <div v-if="products.length" class="mt-10 text-center">
        <NuxtLink to="/products">
          <UButton variant="outline" size="md" class="group/btn">
            Lihat Semua Produk
            <Icon name="lucide:arrow-right" class="w-4 h-4 ml-1.5 group-hover/btn:translate-x-1 transition-transform duration-200" />
          </UButton>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
