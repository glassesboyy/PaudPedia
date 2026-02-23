<script setup lang="ts">
/**
 * Forgot Password Page
 * Route: /auth/forgot-password
 */
definePageMeta({
  layout: 'auth',
  middleware: ['guest'],
})

useSeo({ title: 'Lupa Password' })
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">Lupa Password</h2>
    <!-- TODO: ForgotPasswordForm component -->
    <p class="text-sm text-gray-500 text-center">Form lupa password akan diimplementasikan pada tahap development.</p>
  </div>
</template>
