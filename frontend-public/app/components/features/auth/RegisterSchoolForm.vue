<script setup lang="ts">
/**
 * RegisterSchoolForm — Form for registering a new school (Headmaster onboarding).
 *
 * Two modes:
 * 1. Guest: shows full form (user data + school data)
 * 2. Authenticated: shows only school data (user data pre-filled from auth)
 */
import {
  extractZodErrors,
  parseApiError,
  registerSchoolSchema,
  registerSchoolUpgradeSchema,
  type RegisterFormData,
} from '~~/app/validations/auth'
import { useAuthStore } from '~~/stores/auth'
import { authService } from '~~/services'

const authStore = useAuthStore()
const toast = useToast()
const { isAuthenticated, user } = useAuth()

// Determine form mode
const isUpgrade = computed(() => isAuthenticated.value)

// Full form (guest)
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  school_name: '',
  school_npsn: '',
  school_address: '',
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
  errors.value = {}

  let redirectPath = ''

  if (isUpgrade.value) {
    // Authenticated user — only validate school fields
    const result = registerSchoolUpgradeSchema.safeParse({
      school_name: form.school_name,
      school_npsn: form.school_npsn,
      school_address: form.school_address,
    })
    if (!result.success) {
      errors.value = extractZodErrors(result.error)
      return
    }

    isSubmitting.value = true
    try {
      const response = await authService.registerSchoolUpgrade(result.data)
      // Refresh user data to get updated memberships
      await authStore.fetchUser()
      toast.success('Sekolah berhasil didaftarkan!')
      // Set redirect path
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
  } else {
    // Guest — validate all fields
    const result = registerSchoolSchema.safeParse(form)
    if (!result.success) {
      errors.value = extractZodErrors(result.error)
      return
    }

    isSubmitting.value = true
    try {
      const response = await authStore.registerSchool(result.data)
      toast.success('Registrasi berhasil! Sekolah Anda telah terdaftar.')
      // Set redirect path
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

    <!-- User data section (guest only) -->
    <template v-if="!isUpgrade">
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

      <!-- Divider -->
      <div class="relative py-2">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-border" />
        </div>
        <div class="relative flex justify-center text-xs uppercase">
          <span class="bg-surface px-3 text-muted font-medium">Data Sekolah</span>
        </div>
      </div>
    </template>

    <!-- Authenticated user info -->
    <div v-else class="p-4 rounded-xl bg-surface-muted border border-border-muted">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
          <span class="text-sm font-bold text-primary-600">{{ user?.name?.charAt(0) }}</span>
        </div>
        <div>
          <p class="text-sm font-semibold text-heading">{{ user?.name }}</p>
          <p class="text-xs text-muted">{{ user?.email }}</p>
        </div>
      </div>
    </div>

    <!-- School fields (always shown) -->
    <UInput
      v-model="form.school_name"
      label="Nama Sekolah"
      placeholder="Contoh: TK Melati"
      required
      :error="errors.school_name"
      @clear-error="clearFieldError('school_name')"
    />

    <UInput
      v-model="form.school_npsn"
      label="NPSN"
      placeholder="8 digit NPSN sekolah"
      required
      :error="errors.school_npsn"
      @clear-error="clearFieldError('school_npsn')"
    />

    <UInput
      v-model="form.school_address"
      label="Alamat Sekolah"
      placeholder="Alamat lengkap sekolah"
      required
      :error="errors.school_address"
      @clear-error="clearFieldError('school_address')"
    />

    <UButton type="submit" block :loading="isSubmitting" :disabled="isSubmitting">
      {{ isUpgrade ? 'Daftarkan Sekolah' : 'Daftar & Buat Sekolah' }}
    </UButton>

    <p v-if="!isUpgrade" class="text-center text-sm text-body">
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
