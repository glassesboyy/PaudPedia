<script setup lang="ts">
/**
 * My Products Page
 * Route: /account/products
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Produk Saya' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Produk Saya</h1>
    <!-- TODO: Purchased products list -->
  </div>
</template>
