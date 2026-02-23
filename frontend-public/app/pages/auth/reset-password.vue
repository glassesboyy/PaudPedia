<script setup lang="ts">
/**
 * Reset Password Page
 * Route: /auth/reset-password
 */
definePageMeta({
  layout: 'auth',
  middleware: ['guest'],
})

useSeo({ title: 'Reset Password' })
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">Reset Password</h2>
    <!-- TODO: ResetPasswordForm component -->
    <p class="text-sm text-gray-500 text-center">Form reset password akan diimplementasikan pada tahap development.</p>
  </div>
</template>
