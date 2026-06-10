<script setup lang="ts">
import { extractZodErrors, parseApiError, registerSchema, type RegisterFormData } from '~~/app/validations/auth'
import { useAuthStore } from '~~/stores/auth'

const authStore = useAuthStore()
const toast = useToast()

const form = reactive<RegisterFormData>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = ref<Record<string, string | undefined>>({})
const apiError = ref('')
const isSubmitting = ref(false)

function clearFieldError(field: string) {
  delete errors.value[field]
  apiError.value = ''
}

async function handleSubmit() {
  apiError.value = ''

  const result = registerSchema.safeParse(form)
  if (!result.success) {
    errors.value = extractZodErrors(result.error)
    return
  }

  errors.value = {}
  isSubmitting.value = true

  let redirectPath = ''

  try {
    await authStore.register(result.data)
    toast.success('Registrasi berhasil! Silakan cek email untuk verifikasi.')
    redirectPath = '/auth/verify-email'
  } catch (err: unknown) {
    const { fieldErrors, message } = parseApiError(err)
    if (Object.keys(fieldErrors).length > 0) {
      errors.value = fieldErrors
    } else {
      apiError.value = message
    }
  } finally {
    isSubmitting.value = false
  }

  if (redirectPath) {
    await navigateTo(redirectPath)
  }
}
</script>

<template>
  <form class="space-y-5" @submit.prevent="handleSubmit">
    <UAlert v-if="apiError" variant="error" dismissible>
      {{ apiError }}
    </UAlert>

    <UInput
      v-model="form.name"
      label="Nama Lengkap"
      placeholder="Masukkan nama lengkap"
      autocomplete="name"
      required
      :error="errors.name"
      @clear-error="clearFieldError('name')"
    />

    <UInput
      v-model="form.email"
      label="Email"
      type="email"
      placeholder="nama@email.com"
      autocomplete="email"
      required
      :error="errors.email"
      @clear-error="clearFieldError('email')"
    />

    <UInput
      v-model="form.password"
      label="Password"
      type="password"
      placeholder="Minimal 8 karakter"
      autocomplete="new-password"
      required
      :error="errors.password"
      @clear-error="clearFieldError('password')"
    />

    <UInput
      v-model="form.password_confirmation"
      label="Konfirmasi Password"
      type="password"
      placeholder="Ulangi password"
      autocomplete="new-password"
      required
      :error="errors.password_confirmation"
      @clear-error="clearFieldError('password_confirmation')"
    />

    <UButton type="submit" block :loading="isSubmitting" :disabled="isSubmitting">
      Daftar
    </UButton>

    <p class="text-center text-sm text-body">
      Sudah punya akun?
      <NuxtLink
        to="/auth/login"
        class="text-primary-600 hover:text-primary-700 font-medium transition-colors"
      >
        Masuk
      </NuxtLink>
    </p>
  </form>
</template>
