<script setup lang="ts">
/**
 * CartEmpty — Empty cart state display.
 *
 * Shows an icon, message, and CTA button to browse courses.
 * Uses design system tokens exclusively.
 */
</script>

<template>
  <div class="flex flex-col items-center justify-center py-20 text-center">
    <!-- Icon with gradient background -->
    <div class="relative w-24 h-24 rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100/50 flex items-center justify-center mb-8 shadow-sm">
      <Icon name="lucide:shopping-cart" class="w-11 h-11 text-primary-400" />
      <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-surface border-2 border-border-muted flex items-center justify-center">
        <Icon name="lucide:plus" class="w-2.5 h-2.5 text-muted" />
      </div>
    </div>

    <h2 class="text-xl font-bold text-heading mb-2">Keranjang Kosong</h2>
    <p class="text-sm text-muted max-w-sm mb-8 leading-relaxed">
      Belum ada item di keranjang Anda. Jelajahi kursus, webinar, dan produk digital kami.
    </p>

    <!-- CTA Buttons -->
    <div class="flex flex-wrap items-center justify-center gap-3">
      <NuxtLink
        to="/courses"
        class="group inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-all shadow-sm hover:shadow-md"
      >
        <Icon name="lucide:graduation-cap" class="w-4 h-4" />
        Jelajahi Kursus
        <Icon name="lucide:arrow-right" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" />
      </NuxtLink>
      <NuxtLink
        to="/webinars"
        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-border text-sm font-medium text-body hover:border-secondary-300 hover:text-secondary-600 hover:bg-secondary-50/50 transition-all"
      >
        <Icon name="lucide:video" class="w-4 h-4" />
        Lihat Webinar
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
