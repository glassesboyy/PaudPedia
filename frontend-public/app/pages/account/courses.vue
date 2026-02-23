<script setup lang="ts">
/**
 * My Courses Page
 * Route: /account/courses
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Kursus Saya' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Kursus Saya</h1>
    <!-- TODO: Enrolled courses list -->
  </div>
</template>
