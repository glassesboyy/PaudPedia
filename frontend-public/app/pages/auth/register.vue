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
    <h2 class="text-2xl font-bold text-center mb-6">Daftar Akun</h2>
    <!-- TODO: RegisterForm component -->
    <p class="text-sm text-muted text-center">Form registrasi akan diimplementasikan pada tahap development.</p>
  </div>
</template>
