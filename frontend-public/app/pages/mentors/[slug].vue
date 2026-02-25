<script setup lang="ts">
/**
 * Mentor Profile Page
 * Route: /mentors/:slug
 */
const route = useRoute()
const slug = route.params.slug as string

useSeo({ title: `Mentor - ${slug}` })
</script>

<template>
  <div class="container py-8">
    <h1 class="text-3xl font-bold">Profil Mentor</h1>
    <!-- TODO: MentorProfile component -->
  </div>
</template>
