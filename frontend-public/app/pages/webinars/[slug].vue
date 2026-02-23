<script setup lang="ts">
/**
 * Webinar Detail Page
 * Route: /webinars/:slug
 */
const route = useRoute()
const slug = route.params.slug as string

useSeo({ title: `Webinar - ${slug}` })
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold">Detail Webinar</h1>
    <!-- TODO: Webinar detail components -->
  </div>
</template>
