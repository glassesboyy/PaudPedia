<script setup lang="ts">
import { changePasswordSchema, extractZodErrors, parseApiError, type ChangePasswordFormData } from '~~/app/validations/auth'
import { authService } from '~~/services'

const toast = useToast()

const form = reactive<ChangePasswordFormData>({
  current_password: '',
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

function resetForm() {
  form.current_password = ''
  form.password = ''
  form.password_confirmation = ''
  errors.value = {}
  apiError.value = ''
}

async function handleSubmit() {
  apiError.value = ''

  const result = changePasswordSchema.safeParse(form)
  if (!result.success) {
    errors.value = extractZodErrors(result.error)
    return
  }

  errors.value = {}
  isSubmitting.value = true

  try {
    await authService.changePassword(result.data)
    toast.success('Password berhasil diubah')
    resetForm()
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
    <UAlert v-if="apiError" variant="error" dismissible>{{ apiError }}</UAlert>

    <UInput
      v-model="form.current_password"
      label="Password Saat Ini"
      type="password"
      placeholder="Masukkan password saat ini"
      autocomplete="current-password"
      required
      :error="errors.current_password"
      @clear-error="clearFieldError('current_password')"
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
      label="Konfirmasi Password Baru"
      type="password"
      placeholder="Ulangi password baru"
      autocomplete="new-password"
      required
      :error="errors.password_confirmation"
      @clear-error="clearFieldError('password_confirmation')"
    />

    <UButton type="submit" :loading="isSubmitting" :disabled="isSubmitting">
      Ubah Password
    </UButton>
  </form>
</template>
