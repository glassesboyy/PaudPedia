<script setup lang="ts">
/**
 * LoginView — SIAKAD login form.
 *
 * Features:
 *   - Email + password fields with client-side validation
 *   - "Remember me" is handled via persisted store
 *   - Error handling with BaseAlert
 *   - Redirect after login (from query param or to dashboard/select-school)
 *   - Links to: forgot password, register on frontend-public
 */
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseInput from '@/components/ui/Input/Input.vue'
import BaseAlert from '@/components/ui/Alert/Alert.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()

const publicUrl = import.meta.env.VITE_PUBLIC_URL || 'http://localhost:3000'

const form = ref({
  email: '',
  password: '',
})

const errors = ref<Record<string, string>>({})
const generalError = ref('')
const isSubmitting = ref(false)

const isFormValid = computed(() => {
  return form.value.email.trim() !== '' && form.value.password.trim() !== ''
})

function validate(): boolean {
  errors.value = {}

  if (!form.value.email.trim()) {
    errors.value.email = 'Email wajib diisi'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'Format email tidak valid'
  }

  if (!form.value.password.trim()) {
    errors.value.password = 'Password wajib diisi'
  } else if (form.value.password.length < 6) {
    errors.value.password = 'Password minimal 6 karakter'
  }

  return Object.keys(errors.value).length === 0
}

async function handleLogin() {
  if (!validate()) return

  isSubmitting.value = true
  generalError.value = ''

  try {
    await authStore.login({
      email: form.value.email,
      password: form.value.password,
    })

    // Determine redirect destination
    const redirect = route.query.redirect as string | undefined

    if (redirect) {
      router.push(redirect)
    } else if (schoolStore.memberships.length === 0) {
      // No school — redirect to Frontend Public to register school
      const publicUrl = import.meta.env.VITE_PUBLIC_URL || 'http://localhost:3000'
      window.location.href = `${publicUrl}/auth/register?type=school`
      return
    } else if (schoolStore.memberships.length === 1 && schoolStore.memberships[0]) {
      // Single school — auto-select and go to dashboard
      schoolStore.selectSchool(schoolStore.memberships[0].school_id)
      router.push({ name: 'Dashboard' })
    } else {
      // Multiple schools — show selector
      router.push({ name: 'SelectSchool' })
    }
  } catch (err: unknown) {
    // Handle validation errors from API
    if (err && typeof err === 'object' && err !== null && 'email' in err) {
      const validationErrors = err as Record<string, string[]>
      for (const [key, messages] of Object.entries(validationErrors)) {
        errors.value[key] = messages[0] || 'Invalid input'
      }
    } else {
      generalError.value = 'Email atau password salah. Silakan coba lagi.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div>
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-heading">Masuk ke SIAKAD</h2>
      <p class="mt-2 text-body">
        Masukkan email dan password Anda untuk mengakses sistem.
      </p>
    </div>

    <!-- Error alert -->
    <BaseAlert
      v-if="generalError"
      variant="danger"
      dismissible
      class="mb-6"
      @dismiss="generalError = ''"
    >
      {{ generalError }}
    </BaseAlert>

    <form class="space-y-5" @submit.prevent="handleLogin">
      <BaseInput
        v-model="form.email"
        label="Email"
        type="email"
        placeholder="nama@email.com"
        :error="errors.email"
        required
      >
        <template #prepend>
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
          </svg>
        </template>
      </BaseInput>

      <BaseInput
        v-model="form.password"
        label="Password"
        type="password"
        placeholder="Masukkan password"
        :error="errors.password"
        required
      >
        <template #prepend>
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
          </svg>
        </template>
      </BaseInput>

      <!-- Forgot password link -->
      <div class="flex justify-end">
        <RouterLink to="/forgot-password" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
          Lupa password?
        </RouterLink>
      </div>

      <!-- Submit button -->
      <BaseButton
        type="submit"
        variant="primary"
        size="lg"
        block
        :loading="isSubmitting"
        :disabled="!isFormValid"
      >
        Masuk
      </BaseButton>
    </form>

    <!-- Divider -->
    <div class="relative my-8">
      <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-border-muted" />
      </div>
      <div class="relative flex justify-center text-sm">
        <span class="px-3 bg-background text-muted">Belum punya akun?</span>
      </div>
    </div>

    <!-- Register on public -->
    <p class="text-center text-sm text-body">
      Belum punya akun sekolah?
      <a
        :href="`${publicUrl}/auth/register?type=school`"
        target="_blank"
        class="font-medium text-primary-600 hover:text-primary-700"
      >
        Daftar di PaudPedia.com
        <svg class="w-3.5 h-3.5 inline-block -mt-0.5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
      </a>
    </p>
  </div>
</template>
