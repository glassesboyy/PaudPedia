<script setup lang="ts">
/**
 * Article Detail Page
 * Route: /articles/:slug
 */
const route = useRoute()
const slug = route.params.slug as string

useSeo({ title: `Artikel - ${slug}` })
</script>

<template>
  <div class="container py-8">
    <h1 class="text-3xl font-bold">Detail Artikel</h1>
    <!-- TODO: ArticleContent + ArticleTOC -->
  </div>
</template>
