<script setup lang="ts">
/**
 * LMS Course Overview
 * Route: /learn/:courseSlug
 */
definePageMeta({
  layout: 'lms',
  middleware: ['auth'],
})

const route = useRoute()
const _courseSlug = route.params.courseSlug as string

useSeo({ title: 'Belajar' })
</script>

<template>
  <div class="container py-8">
    <h1 class="text-3xl font-bold">Course Overview</h1>
    <!-- TODO: Course player overview -->
  </div>
</template>
