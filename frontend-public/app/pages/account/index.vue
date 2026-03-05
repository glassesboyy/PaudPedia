<script setup lang="ts">
/**
 * Account Profile Page
 * Route: /account
 *
 * FR-UA-06: Edit profil pengguna (nama, foto, telepon, gender, tanggal lahir, alamat)
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Edit Profil' })
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-heading mb-6">Profil Saya</h1>
    <EditProfileForm />
  </div>
</template>
