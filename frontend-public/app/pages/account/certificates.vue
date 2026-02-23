<script setup lang="ts">
/**
 * My Certificates Page
 * Route: /account/certificates
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Sertifikat Saya' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Sertifikat Saya</h1>
    <!-- TODO: Certificates list -->
  </div>
</template>
