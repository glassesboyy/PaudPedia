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

    <!-- Change Password Section -->
    <section class="max-w-lg">
      <h2 class="text-lg font-semibold text-heading mb-1">Ubah Password</h2>
      <p class="text-sm text-muted mb-5">Pastikan akun Anda menggunakan password yang kuat dan unik.</p>
      <ChangePasswordForm />
    </section>
  </div>
</template>
