<script setup lang="ts">
/**
 * Account Settings Page
 * Route: /account/settings
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Pengaturan Akun' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-heading mb-8">Pengaturan Akun</h1>
        <ChangePasswordForm />
  </div>
</template>
