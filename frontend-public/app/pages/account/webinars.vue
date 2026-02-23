<script setup lang="ts">
/**
 * My Webinars Page
 * Route: /account/webinars
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Webinar Saya' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Webinar Saya</h1>
    <!-- TODO: Registered webinars list -->
  </div>
</template>
