<script setup lang="ts">
/**
 * EditProfileForm — Profile editing form component.
 *
 * Handles text profile fields (name, phone, gender, date_of_birth, address).
 * Avatar upload is handled independently by AvatarUpload component.
 */
import { extractZodErrors, parseApiError } from '~~/app/validations/auth'
import { updateProfileSchema, type UpdateProfileFormData } from '~~/app/validations/profile'
import { authService } from '~~/services'
import { useAuthStore } from '~~/stores/auth'
import type { User } from '~~/types'

const authStore = useAuthStore()
const toast = useToast()

// ── Form state ────────────────────────────────────────────
const form = reactive<UpdateProfileFormData>({
  name: '',
  phone: '',
  gender: null,
  date_of_birth: '',
  address: '',
})

const errors = ref<Record<string, string | undefined>>({})
const apiError = ref('')
const isSubmitting = ref(false)

// ── Populate form from auth store ─────────────────────────
function populateForm(user: User | null) {
  if (!user) return
  form.name = user.name || ''
  form.phone = user.phone || ''
  form.gender = user.gender || null
  form.date_of_birth = user.date_of_birth || ''
  form.address = user.address || ''
}

// Initialize on mount
onMounted(() => {
  populateForm(authStore.user)
})

// Re-populate if user data updates externally (e.g., avatar upload)
watch(() => authStore.user, (newUser) => {
  // Only re-populate if not currently submitting (avoid overwriting user edits)
  if (!isSubmitting.value) {
    populateForm(newUser)
  }
}, { deep: true })

// ── Field error clearing ──────────────────────────────────
function clearFieldError(field: string) {
  delete errors.value[field]
  apiError.value = ''
}

// ── Reset form to stored user data ────────────────────────
function resetForm() {
  populateForm(authStore.user)
  errors.value = {}
  apiError.value = ''
}

// ── Handle avatar update from AvatarUpload ────────────────
function handleAvatarUpdated(user: User) {
  authStore.updateUser(user)
}

// ── Form submission ───────────────────────────────────────
async function handleSubmit() {
  apiError.value = ''

  const result = updateProfileSchema.safeParse(form)
  if (!result.success) {
    errors.value = extractZodErrors(result.error)
    return
  }

  errors.value = {}
  isSubmitting.value = true

  try {
    const response = await authService.updateProfile({
      name: result.data.name,
      phone: result.data.phone || undefined,
      gender: result.data.gender || undefined,
      date_of_birth: result.data.date_of_birth || undefined,
      address: result.data.address || undefined,
    })
    authStore.updateUser(response.data)
    toast.success('Profil berhasil diperbarui')
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
  <div class="space-y-8">
    <!-- Avatar Section -->
    <div class="p-6 rounded-xl border border-border bg-surface shadow-soft">
      <h3 class="text-lg font-semibold text-heading mb-1">Foto Profil</h3>
      <p class="text-xs text-muted mb-5">Unggah foto profil yang jelas untuk mempersonalisasi akun Anda.</p>
      <AvatarUpload
        :current-avatar-url="authStore.user?.avatar_url"
        :user-name="authStore.user?.name || ''"
        @avatar-updated="handleAvatarUpdated"
      />
    </div>

    <!-- Profile Form Section -->
    <div class="p-6 rounded-xl border border-border bg-surface shadow-soft">
      <h3 class="text-lg font-semibold text-heading mb-1">Informasi Profil</h3>
      <p class="text-xs text-muted mb-5">Perbarui informasi profil Anda untuk menjaga akun tetap terkini.</p>

      <form class="space-y-5" @submit.prevent="handleSubmit">
        <UAlert v-if="apiError" variant="error" dismissible>{{ apiError }}</UAlert>

        <!-- Name + Phone: 2-column on desktop -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <UInput
            v-model="form.name"
            label="Nama Lengkap"
            type="text"
            placeholder="Masukkan nama lengkap"
            autocomplete="name"
            required
            :error="errors.name"
            @clear-error="clearFieldError('name')"
          />

          <UInput
            v-model="form.phone"
            label="Nomor Telepon"
            type="tel"
            placeholder="Contoh: 08123456789"
            autocomplete="tel"
            :error="errors.phone"
            @clear-error="clearFieldError('phone')"
          />
        </div>

        <!-- Gender + Date of Birth: 2-column on desktop -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <!-- Gender select (styled consistent with UInput) -->
          <div>
            <label class="block text-sm font-medium text-foreground mb-1.5">
              Jenis Kelamin
            </label>
            <select
              v-model="form.gender"
              :class="[
                'block w-full rounded-lg border px-3 py-2.5 text-sm',
                'text-foreground bg-surface',
                'transition-colors duration-200',
                'focus:outline-none focus:ring-2 focus:ring-offset-0',
                errors.gender
                  ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/30'
                  : 'border-border focus:border-primary-500 focus:ring-primary-500/30',
              ]"
              @change="clearFieldError('gender')"
            >
              <option :value="null" disabled>Pilih jenis kelamin</option>
              <option value="male">Laki-laki</option>
              <option value="female">Perempuan</option>
            </select>
            <p v-if="errors.gender" class="mt-1.5 text-sm text-danger-600">{{ errors.gender }}</p>
          </div>

          <UInput
            v-model="form.date_of_birth"
            label="Tanggal Lahir"
            type="date"
            autocomplete="bday"
            :error="errors.date_of_birth"
            @clear-error="clearFieldError('date_of_birth')"
          />
        </div>

        <!-- Address: full width -->
        <div>
          <label class="block text-sm font-medium text-foreground mb-1.5">
            Alamat
          </label>
          <textarea
            v-model="form.address"
            rows="3"
            placeholder="Masukkan alamat lengkap"
            autocomplete="street-address"
            :class="[
              'block w-full rounded-lg border px-3 py-2.5 text-sm',
              'text-foreground bg-surface placeholder:text-muted',
              'transition-colors duration-200 resize-none',
              'focus:outline-none focus:ring-2 focus:ring-offset-0',
              errors.address
                ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/30'
                : 'border-border focus:border-primary-500 focus:ring-primary-500/30',
            ]"
            @input="clearFieldError('address')"
          />
          <p v-if="errors.address" class="mt-1.5 text-sm text-danger-600">{{ errors.address }}</p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
          <UButton
            variant="outline"
            :disabled="isSubmitting"
            @click="resetForm"
          >
            Batal
          </UButton>
          <UButton
            type="submit"
            :loading="isSubmitting"
            :disabled="isSubmitting"
          >
            Simpan Perubahan
          </UButton>
        </div>
      </form>
    </div>
  </div>
</template>
