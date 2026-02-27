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
    <h2 class="text-2xl font-bold text-center text-heading mb-6">Lupa Password</h2>
    <ForgotPasswordForm />
  </div>
</template>
