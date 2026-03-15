<script setup lang="ts">
/**
 * CartEmpty — Empty cart state display.
 *
 * Shows an icon, message, and CTA button to browse courses.
 * Uses design system tokens exclusively.
 */
</script>

<template>
  <div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-20 h-20 rounded-2xl bg-primary-50 flex items-center justify-center mb-6">
      <Icon name="lucide:shopping-cart" class="w-10 h-10 text-primary-300" />
    </div>
    <h2 class="text-xl font-bold text-heading mb-2">Keranjang Kosong</h2>
    <p class="text-sm text-muted max-w-sm mb-8">
      Belum ada item di keranjang Anda. Jelajahi kursus, webinar, dan produk digital kami.
    </p>
    <div class="flex flex-wrap items-center justify-center gap-3">
      <NuxtLink
        to="/courses"
        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm"
      >
        <Icon name="lucide:graduation-cap" class="w-4 h-4" />
        Jelajahi Kursus
      </NuxtLink>
      <NuxtLink
        to="/products"
        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-border text-sm font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all"
      >
        <Icon name="lucide:package" class="w-4 h-4" />
        Lihat Produk
      </NuxtLink>
    </div>
  </div>
</template>
