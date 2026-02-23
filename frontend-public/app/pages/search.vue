<script setup lang="ts">
/**
 * Search Page
 * Route: /search
 */
const route = useRoute()
const query = computed(() => route.query.q as string || '')

useSeo({ title: query.value ? `Hasil Pencarian: ${query.value}` : 'Pencarian' })
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold">
      {{ query ? `Hasil Pencarian: "${query}"` : 'Pencarian' }}
    </h1>
    <!-- TODO: Search results with filters -->
  </div>
</template>
