<script setup lang="ts">
import { onMounted } from 'vue'

const publicUrl = import.meta.env.VITE_PUBLIC_URL || 'http://localhost:3000'

onMounted(() => {
  window.location.href = `${publicUrl}/auth/forgot-password`
})
</script>

<template>
  <div class="text-center py-12">
    <p class="text-slate-500">Mengalihkan ke halaman pemulihan kata sandi...</p>
    <a :href="`${publicUrl}/auth/forgot-password`" class="text-primary-600 font-medium text-sm mt-4 inline-block">
      Klik di sini jika tidak otomatis dialihkan
    </a>
  </div>
</template>
