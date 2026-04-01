<script setup lang="ts">
/**
 * TokenCallbackView — Handles cross-domain auth token from Frontend Public.
 *
 * Route: /auth/token?token=xxx
 *
 * Flow:
 * 1. Reads `token` from query param
 * 2. Stores token in auth store
 * 3. Fetches user data via /me
 * 4. Redirects to /select-school or /dashboard
 */
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import { useRouter, useRoute } from 'vue-router'
import { onMounted, ref } from 'vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()
const error = ref('')

onMounted(async () => {
  const token = route.query.token as string | undefined

  if (!token) {
    error.value = 'Token tidak ditemukan. Silakan login ulang.'
    setTimeout(() => router.push({ name: 'Login' }), 2000)
    return
  }

  try {
    // Store the token and handle user data initialization
    await authStore.setAuth(token)

    if (!authStore.isAuthenticated) {
      error.value = 'Data profil user gagal diverifikasi. Silakan login ulang.'
      setTimeout(() => router.push({ name: 'Login' }), 2000)
      return
    }

    if (schoolStore.memberships.length === 0) {
      // No schools — redirect to Public frontend as per LoginView logic
      error.value = 'Anda belum terdaftar di sekolah manapun.'
      setTimeout(() => {
        const publicUrl = import.meta.env.VITE_PUBLIC_URL || 'http://localhost:3000'
        window.location.href = `${publicUrl}/auth/register?type=school`
      }, 2000)
    } else if (schoolStore.memberships.length === 1 && schoolStore.memberships[0]) {
      // Single school — auto-select
      schoolStore.selectSchool(schoolStore.memberships[0].school_id)
      router.push({ name: 'Dashboard' })
    } else {
      // Multiple schools — let user choose
      router.push({ name: 'SelectSchool' })
    }
  } catch (err) {
    console.error('[AuthTokenError]', err)
    error.value = 'Gagal memproses autentikasi. Token mungkin kadaluarsa.'
    setTimeout(() => router.push({ name: 'Login' }), 2000)
  }
})
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-background gap-4">
    <!-- Loading state -->
    <template v-if="!error">
      <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin" />
      <p class="text-sm text-muted">Memproses autentikasi...</p>
    </template>

    <!-- Error state -->
    <template v-else>
      <div class="w-12 h-12 rounded-full bg-danger-50 flex items-center justify-center">
        <Icon name="lucide:x" class="w-6 h-6 text-danger-500" stroke-width="2" />
      </div>
      <p class="text-sm text-danger-600 font-medium">{{ error }}</p>
      <p class="text-xs text-muted">Mengalihkan ke halaman login...</p>
    </template>
  </div>
</template>
