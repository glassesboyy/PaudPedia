<script setup lang="ts">
/**
 * Email Verification Page
 * Route: /auth/verify-email/[id]/[hash]
 */
definePageMeta({
  layout: 'auth',
})

const route = useRoute()
const _id = route.params.id
const _hash = route.params.hash

useSeo({ title: 'Verifikasi Email' })
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">Verifikasi Email</h2>
    <p class="text-sm text-gray-500 text-center">Halaman verifikasi email akan diimplementasikan pada tahap development.</p>
  </div>
</template>
