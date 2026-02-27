<script setup lang="ts">
/**
 * Register Page
 * Route: /auth/register
 */
definePageMeta({
  layout: 'auth',
  middleware: ['guest'],
})

useSeo({ title: 'Daftar' })
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-center text-heading mb-6">Daftar Akun</h2>
    <RegisterForm />
  </div>
</template>
