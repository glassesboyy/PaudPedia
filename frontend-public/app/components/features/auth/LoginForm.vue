<script setup lang="ts">
import { extractZodErrors, loginSchema, parseApiError, type LoginFormData } from '~~/app/validations/auth'
import { useAuthStore } from '~~/stores/auth'

const authStore = useAuthStore()
const toast = useToast()
const route = useRoute()

const form = reactive<LoginFormData>({
  email: '',
  password: '',
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

  const result = loginSchema.safeParse(form)
  if (!result.success) {
    errors.value = extractZodErrors(result.error)
    return
  }

  errors.value = {}
  isSubmitting.value = true

  try {
    const response = await authStore.login(result.data)
    toast.success('Berhasil masuk')

    // Redirect: if email not verified → verify page, else → original target
    if (!response.data.email_verified) {
      await navigateTo('/auth/verify-email')
    } else {
      const redirect = (route.query.redirect as string) || '/account'
      await navigateTo(redirect)
    }
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
    <UAlert v-if="apiError" variant="error" dismissible>
      {{ apiError }}
    </UAlert>

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
      placeholder="Masukkan password"
      autocomplete="current-password"
      required
      :error="errors.password"
      @clear-error="clearFieldError('password')"
    />

    <div class="flex items-center justify-between">
      <NuxtLink
        to="/auth/forgot-password"
        class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors"
      >
        Lupa password?
      </NuxtLink>
    </div>

    <UButton type="submit" block :loading="isSubmitting" :disabled="isSubmitting">
      Masuk
    </UButton>

    <p class="text-center text-sm text-body">
      Belum punya akun?
      <NuxtLink
        to="/auth/register"
        class="text-primary-600 hover:text-primary-700 font-medium transition-colors"
      >
        Daftar
      </NuxtLink>
    </p>
  </form>
</template>
