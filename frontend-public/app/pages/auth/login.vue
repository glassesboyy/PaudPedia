<script setup lang="ts">
/**
 * Login Page
 * Route: /auth/login
 */
definePageMeta({
  layout: 'auth',
  middleware: ['guest'],
})

useSeo({ title: 'Masuk' })
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-center text-heading mb-6">Masuk</h2>
    <LoginForm />
  </div>
</template>
