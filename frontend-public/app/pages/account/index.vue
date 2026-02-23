<script setup lang="ts">
/**
 * Account Profile Page
 * Route: /account
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Profil Saya' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">Profil Saya</h1>
    <!-- TODO: Profile form -->
  </div>
</template>
