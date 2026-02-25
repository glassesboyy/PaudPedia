<script setup lang="ts">
/**
 * Course Detail Page
 * Route: /courses/:slug
 */
const route = useRoute()
const slug = route.params.slug as string

useSeo({
  title: `Kursus - ${slug}`,
})
</script>

<template>
  <div class="container py-8">
    <h1 class="text-3xl font-bold">Detail Kursus</h1>
    <!-- TODO: Course detail components -->
  </div>
</template>
