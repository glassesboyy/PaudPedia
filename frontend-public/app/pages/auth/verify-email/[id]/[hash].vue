<script setup lang="ts">
/**
 * Email Verification Handler
 * Route: /auth/verify-email/[id]/[hash]?expires=...&signature=...
 *
 * Automatically verifies the email when the page loads.
 */
import { authService } from '~~/services'
import { useAuthStore } from '~~/stores/auth'

definePageMeta({ layout: 'auth' })
useSeo({ title: 'Verifikasi Email' })

const route = useRoute()
const toast = useToast()
const authStore = useAuthStore()

const id = route.params.id as string
const hash = route.params.hash as string

const isVerifying = ref(true)
const isSuccess = ref(false)
const errorMessage = ref('')

onMounted(async () => {
  try {
    // Forward signature query params to the API
    const query: Record<string, string> = {}
    if (route.query.expires) query.expires = route.query.expires as string
    if (route.query.signature) query.signature = route.query.signature as string

    await authService.verifyEmail(id, hash, query)
    isSuccess.value = true
    toast.success('Email berhasil diverifikasi!')

    // Refresh user data so email_verified_at is updated
    if (authStore.isAuthenticated) {
      await authStore.fetchUser()
    }
  } catch (err: unknown) {
    const e = err as { data?: { message?: string } }
    errorMessage.value = e?.data?.message || 'Verifikasi gagal. Link mungkin sudah kedaluwarsa.'
  } finally {
    isVerifying.value = false
  }
})
</script>

<template>
  <div class="text-center space-y-5">
    <!-- Loading -->
    <template v-if="isVerifying">
      <div class="mx-auto w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center animate-pulse">
        <Icon name="lucide:loader-2" class="w-8 h-8 text-primary-600 animate-spin" />
      </div>
      <p class="text-body">Memverifikasi email Anda...</p>
    </template>

    <!-- Success -->
    <template v-else-if="isSuccess">
      <div class="mx-auto w-16 h-16 rounded-full bg-success-100 flex items-center justify-center">
        <Icon name="lucide:check-circle" class="w-8 h-8 text-success-600" />
      </div>
      <div>
        <h2 class="text-2xl font-bold text-heading">Email Terverifikasi!</h2>
        <p class="mt-2 text-sm text-body">Email Anda berhasil diverifikasi. Anda sekarang bisa mengakses semua fitur.</p>
      </div>
      <NuxtLink to="/account">
        <UButton variant="primary" block>Ke Dashboard</UButton>
      </NuxtLink>
    </template>

    <!-- Error -->
    <template v-else>
      <div class="mx-auto w-16 h-16 rounded-full bg-danger-100 flex items-center justify-center">
        <Icon name="lucide:alert-circle" class="w-8 h-8 text-danger-600" />
      </div>
      <div>
        <h2 class="text-2xl font-bold text-heading">Verifikasi Gagal</h2>
        <p class="mt-2 text-sm text-body">{{ errorMessage }}</p>
      </div>
      <div class="space-y-2">
        <NuxtLink to="/auth/verify-email">
          <UButton variant="outline" block>Kirim Ulang Email Verifikasi</UButton>
        </NuxtLink>
        <NuxtLink
          to="/auth/login"
          class="block text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors"
        >
          Kembali ke halaman masuk
        </NuxtLink>
      </div>
    </template>
  </div>
</template>
