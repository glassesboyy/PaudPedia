<script setup lang="ts">
import { extractZodErrors, parseApiError, resetPasswordSchema } from '~~/app/validations/auth'
import { authService } from '~~/services'

const route = useRoute()
const toast = useToast()

const token = (route.query.token as string) || ''

const form = reactive({
  email: (route.query.email as string) || '',
  password: '',
  password_confirmation: '',
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

  const result = resetPasswordSchema.safeParse(form)
  if (!result.success) {
    errors.value = extractZodErrors(result.error)
    return
  }

  if (!token) {
    apiError.value = 'Token reset tidak valid. Silakan minta link reset baru.'
    return
  }

  errors.value = {}
  isSubmitting.value = true

  try {
    await authService.resetPassword({ ...result.data, token })
    isSuccess.value = true
    toast.success('Password berhasil direset')
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
        Password Anda berhasil direset. Silakan masuk dengan password baru.
      </UAlert>
      <NuxtLink to="/auth/login">
        <UButton variant="primary" block>Masuk</UButton>
      </NuxtLink>
    </template>

    <!-- Form state -->
    <template v-else>
      <UAlert v-if="!token" variant="warning">
        Link reset tidak valid atau sudah kedaluwarsa. Silakan
        <NuxtLink to="/auth/forgot-password" class="underline font-medium">minta link baru</NuxtLink>.
      </UAlert>

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

      <UInput
        v-model="form.password"
        label="Password Baru"
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
        placeholder="Ulangi password baru"
        autocomplete="new-password"
        required
        :error="errors.password_confirmation"
        @clear-error="clearFieldError('password_confirmation')"
      />

      <UButton type="submit" block :loading="isSubmitting" :disabled="!token || isSubmitting">
        Reset Password
      </UButton>
    </template>
  </form>
</template>
