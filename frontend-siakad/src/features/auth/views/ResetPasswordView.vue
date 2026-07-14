<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const publicUrl = import.meta.env.VITE_PUBLIC_URL || 'http://localhost:3000'

onMounted(() => {
  const token = route.query.token || ''
  const email = route.query.email || ''
  window.location.href = `${publicUrl}/auth/reset-password?token=${token}&email=${email}`
})
</script>

<template>
  <div class="text-center py-12">
    <p class="text-slate-500">Mengalihkan ke halaman atur ulang kata sandi...</p>
    <a :href="`${publicUrl}/auth/reset-password`" class="text-primary-600 font-medium text-sm mt-4 inline-block">
      Klik di sini jika tidak otomatis dialihkan
    </a>
  </div>
</template>
