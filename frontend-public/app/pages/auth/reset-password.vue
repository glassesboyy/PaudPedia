<script setup lang="ts">
/**
 * Reset Password Page
 * Route: /auth/reset-password?token=...&email=...
 */
definePageMeta({
  layout: 'auth',
  middleware: ['guest'],
})

useSeo({ title: 'Reset Password' })
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-center text-heading mb-6">Reset Password</h2>
    <ResetPasswordForm />
  </div>
</template>
