<script setup lang="ts">
/**
 * Checkout Page
 * Route: /checkout
 */
definePageMeta({
  layout: 'minimal',
  middleware: ['auth'],
})

useSeo({ title: 'Checkout' })
</script>

<template>
  <div class="container py-8 max-w-3xl">
    <h1 class="text-3xl font-bold">Checkout</h1>
    <!-- TODO: CheckoutForm + PaymentMethods -->
  </div>
</template>
