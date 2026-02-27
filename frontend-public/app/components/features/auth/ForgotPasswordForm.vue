<script setup lang="ts">
import { extractZodErrors, forgotPasswordSchema, parseApiError, type ForgotPasswordFormData } from '~~/app/validations/auth'
import { authService } from '~~/services'

const form = reactive<ForgotPasswordFormData>({
  email: '',
})

const errors = ref<Record<string, string | undefined>>({})
const apiError = ref('')
const isSubmitting = ref(false)
const isSuccess = ref(false)

function clearFieldError(field: string) {
  delete errors.value[field]
  apiError.value = ''
}

async function handleSubmit() {
  apiError.value = ''
  isSuccess.value = false

  const result = forgotPasswordSchema.safeParse(form)
  if (!result.success) {
    errors.value = extractZodErrors(result.error)
    return
  }

  errors.value = {}
  isSubmitting.value = true

  try {
    await authService.forgotPassword(result.data.email)
    isSuccess.value = true
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
}
</script>

<template>
  <form class="space-y-5" @submit.prevent="handleSubmit">
    <!-- Success state -->
    <template v-if="isSuccess">
      <UAlert variant="success">
        Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.
      </UAlert>
      <NuxtLink
        to="/auth/login"
        class="block text-center text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors"
      >
        Kembali ke halaman masuk
      </NuxtLink>
    </template>

    <!-- Form state -->
    <template v-else>
      <p class="text-sm text-body text-center">
        Masukkan email yang terdaftar dan kami akan mengirimkan link untuk reset password.
      </p>

      <UAlert v-if="apiError" variant="error" dismissible>{{ apiError }}</UAlert>

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

      <UButton type="submit" block :loading="isSubmitting" :disabled="isSubmitting">
        Kirim Link Reset
      </UButton>

      <NuxtLink
        to="/auth/login"
        class="block text-center text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors"
      >
        Kembali ke halaman masuk
      </NuxtLink>
    </template>
  </form>
</template>
