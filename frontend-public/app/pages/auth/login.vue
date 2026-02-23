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
    <h2 class="text-2xl font-bold text-center mb-6">Masuk</h2>
    <!-- TODO: LoginForm component -->
    <p class="text-sm text-gray-500 text-center">Form login akan diimplementasikan pada tahap development.</p>
  </div>
</template>
