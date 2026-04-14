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
  } catch (err: any) {
    // Handle validation errors from API
    const apiErrors = err.response?.data?.errors
    
    if (apiErrors) {
      for (const [key, messages] of Object.entries(apiErrors)) {
        errors.value[key] = (messages as string[])[0] || 'Invalid input'
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
          <Icon name="lucide:mail" class="w-5 h-5 text-slate-400" />
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
          <Icon name="lucide:lock" class="w-5 h-5 text-slate-400" />
        </template>
      </BaseInput>

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

    <!-- Register on public -->
    <p class="text-center text-sm text-body text-muted my-8">
      Belum punya akun sekolah?
      <a
        :href="`${publicUrl}/auth/register?type=school`"
        target="_blank"
        class="font-medium text-primary-600 hover:text-primary-700"
      >
        Daftar di PaudPedia.com
        <Icon name="lucide:external-link" class="w-3.5 h-3.5 inline-block -mt-0.5 ml-0.5" />
      </a>
    </p>
  </div>
</template>
