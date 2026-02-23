<script setup lang="ts">
/**
 * My Orders Page
 * Route: /account/orders
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Riwayat Pesanan' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Riwayat Pesanan</h1>
    <!-- TODO: Order history list -->
  </div>
</template>
