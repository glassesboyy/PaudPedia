<script setup lang="ts">
/**
 * Verify Email — "Check your inbox" page.
 *
 * Route: /auth/verify-email
 * Shown after registration or when email-verified middleware redirects here.
 * Allows resending the verification email.
 */
import { authService } from '~~/services'

definePageMeta({ layout: 'auth' })
useSeo({ title: 'Verifikasi Email' })

const { user, isAuthenticated } = useAuth()
const toast = useToast()

const isResending = ref(false)
const resendCooldown = ref(0)
let cooldownTimer: ReturnType<typeof setInterval> | null = null

async function resendEmail() {
  if (resendCooldown.value > 0) return

  isResending.value = true
  try {
    await authService.resendVerificationEmail()
    toast.success('Email verifikasi telah dikirim ulang')
    resendCooldown.value = 60
    cooldownTimer = setInterval(() => {
      resendCooldown.value--
      if (resendCooldown.value <= 0 && cooldownTimer) {
        clearInterval(cooldownTimer)
        cooldownTimer = null
      }
    }, 1000)
  } catch {
    toast.error('Gagal mengirim ulang email verifikasi. Coba lagi nanti.')
  } finally {
    isResending.value = false
  }
}

onUnmounted(() => {
  if (cooldownTimer) clearInterval(cooldownTimer)
})
</script>

<template>
  <div class="text-center space-y-5">
    <div class="mx-auto w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
      <Icon name="lucide:mail-check" class="w-8 h-8 text-primary-600" />
    </div>

    <div>
      <h2 class="text-2xl font-bold text-heading">Verifikasi Email Anda</h2>
      <p class="mt-2 text-sm text-body">
        Kami telah mengirimkan link verifikasi ke
        <span v-if="user" class="font-medium text-foreground">{{ user.email }}</span>.
        Silakan cek inbox atau folder spam Anda.
      </p>
    </div>

    <!-- Resend button (only for authenticated users) -->
    <template v-if="isAuthenticated">
      <UButton
        variant="outline"
        block
        :loading="isResending"
        :disabled="resendCooldown > 0 || isResending"
        @click="resendEmail"
      >
        {{ resendCooldown > 0 ? `Kirim ulang (${resendCooldown}s)` : 'Kirim Ulang Email Verifikasi' }}
      </UButton>
    </template>

    <div class="pt-2 space-y-2">
      <NuxtLink
        v-if="isAuthenticated"
        to="/"
        class="block text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors"
      >
        Kembali ke Beranda
      </NuxtLink>
      <NuxtLink
        v-else
        to="/auth/login"
        class="block text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors"
      >
        Kembali ke halaman masuk
      </NuxtLink>
    </div>
  </div>
</template>
