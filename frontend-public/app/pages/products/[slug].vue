<script setup lang="ts">
/**
 * Product Detail Page
 * Route: /products/:slug
 */
const route = useRoute()
const slug = route.params.slug as string

useSeo({ title: `Produk - ${slug}` })
</script>

<template>
  <div class="container py-8">
    <h1 class="text-3xl font-bold">Detail Produk</h1>
    <!-- TODO: Product detail components -->
  </div>
</template>
