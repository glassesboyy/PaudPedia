<script setup lang="ts">
/**
 * AvatarUpload — Self-contained avatar upload/remove component.
 *
 * Handles file selection, client-side validation, upload, and removal
 * independently from the profile form.
 */
import { parseApiError } from '~~/app/validations/auth';
import { authService } from '~~/services';

interface Props {
  currentAvatarUrl?: string | null
  userName: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'avatar-updated': [user: import('~~/types').User]
}>()

const toast = useToast()
const fileInput = ref<HTMLInputElement | null>(null)
const previewUrl = ref<string | null>(null)
const error = ref('')
const isUploading = ref(false)
const isRemoving = ref(false)

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp']
const MAX_SIZE = 2 * 1024 * 1024 // 2MB

const displayUrl = computed(() => previewUrl.value || props.currentAvatarUrl || null)

function triggerFileSelect() {
  fileInput.value?.click()
}

function validateFile(file: File): string | null {
  if (!ALLOWED_TYPES.includes(file.type)) {
    return 'Format gambar harus JPG, PNG, atau WebP.'
  }
  if (file.size > MAX_SIZE) {
    return 'Ukuran gambar maksimal 2MB.'
  }
  return null
}

async function handleFileChange(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return

  // Reset input so same file can be selected again
  input.value = ''

  error.value = ''
  const validationError = validateFile(file)
  if (validationError) {
    error.value = validationError
    return
  }

  // Show preview immediately
  previewUrl.value = URL.createObjectURL(file)
  isUploading.value = true

  try {
    const response = await authService.uploadAvatar(file)
    emit('avatar-updated', response.data)
    toast.success('Avatar berhasil diperbarui')
    // Clear preview, the real URL will come from props update
    if (previewUrl.value) {
      URL.revokeObjectURL(previewUrl.value)
      previewUrl.value = null
    }
  } catch (err: unknown) {
    const { message } = parseApiError(err)
    error.value = message || 'Gagal mengunggah avatar.'
    // Revert preview on failure
    if (previewUrl.value) {
      URL.revokeObjectURL(previewUrl.value)
      previewUrl.value = null
    }
  } finally {
    isUploading.value = false
  }
}

async function handleRemove() {
  if (!props.currentAvatarUrl) return

  error.value = ''
  isRemoving.value = true

  try {
    const response = await authService.removeAvatar()
    emit('avatar-updated', response.data)
    toast.success('Avatar berhasil dihapus')
  } catch (err: unknown) {
    const { message } = parseApiError(err)
    error.value = message || 'Gagal menghapus avatar.'
  } finally {
    isRemoving.value = false
  }
}

// Cleanup object URLs on unmount
onUnmounted(() => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
  }
})
</script>

<template>
  <div class="flex flex-col items-center gap-3 sm:flex-row sm:items-start sm:gap-5">
    <!-- Avatar display with hover overlay -->
    <div class="relative group">
      <UAvatar
        :src="displayUrl"
        :name="userName"
        size="xl"
      />
      <!-- Upload overlay -->
      <button
        type="button"
        :disabled="isUploading"
        class="
          absolute inset-0 rounded-full flex items-center justify-center
          bg-black/40 opacity-0 group-hover:opacity-100
          transition-opacity duration-200 cursor-pointer
          disabled:cursor-not-allowed
        "
        @click="triggerFileSelect"
      >
        <Icon name="lucide:camera" class="w-6 h-6 text-white" />
      </button>
      <!-- Loading spinner overlay -->
      <div
        v-if="isUploading"
        class="absolute inset-0 rounded-full flex items-center justify-center bg-black/50"
      >
        <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
      </div>
    </div>

    <!-- Actions + info -->
    <div class="flex flex-col items-center sm:items-start gap-2">
      <div class="flex gap-2">
        <UButton
          size="sm"
          variant="outline"
          :disabled="isUploading || isRemoving"
          @click="triggerFileSelect"
        >
          <Icon name="lucide:upload" class="w-4 h-4 mr-1.5" />
          Ubah Foto
        </UButton>
        <UButton
          v-if="currentAvatarUrl"
          size="sm"
          variant="ghost"
          :loading="isRemoving"
          :disabled="isUploading || isRemoving"
          @click="handleRemove"
        >
          <Icon name="lucide:trash-2" class="w-4 h-4 mr-1.5" />
          Hapus
        </UButton>
      </div>
      <p class="text-xs text-muted">JPG, PNG, atau WebP. Maks 2MB.</p>
      <p v-if="error" class="text-xs text-danger-600">{{ error }}</p>
    </div>

    <!-- Hidden file input -->
    <input
      ref="fileInput"
      type="file"
      accept="image/jpeg,image/png,image/webp"
      class="hidden"
      @change="handleFileChange"
    />
  </div>
</template>
