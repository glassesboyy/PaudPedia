<script setup lang="ts">
/**
 * LMS Course Overview (redirect)
 * Route: /learn/:courseSlug
 *
 * Redirects to the preferred lesson (next unfinished or first).
 * Parent [courseSlug].vue handles course data loading.
 */
import { useLmsStore } from '~~/stores/lms'

const route = useRoute()
const store = useLmsStore()
const courseSlug = route.params.courseSlug as string

const isRedirecting = ref(true)
const error = ref('')

async function redirectToLesson() {
  isRedirecting.value = true
  error.value = ''

  try {
    // Wait for parent to finish loading course data
    if (store.isCourseLoading) {
      await new Promise<void>((resolve) => {
        const unwatch = watch(() => store.isCourseLoading, (loading) => {
          if (!loading) {
            unwatch()
            resolve()
          }
        })
      })
    }

    if (store.courseError || !store.playerData) {
      error.value = 'Gagal memuat data kursus.'
      return
    }

    const lastLesson = store.getLastLesson()
    const firstLesson = store.flatLessons[0]
    const targetSlug = store.playerData.next_lesson_slug || lastLesson || firstLesson?.slug

    if (!targetSlug) {
      error.value = 'Kursus ini belum memiliki materi untuk dipelajari.'
      return
    }

    await navigateTo(`/learn/${courseSlug}/${targetSlug}`, { replace: true })
  } catch {
    error.value = 'Gagal memuat data pembelajaran. Silakan coba lagi.'
  } finally {
    isRedirecting.value = false
  }
}

onMounted(redirectToLesson)
</script>

<template>
  <div class="min-h-[60vh] flex items-center justify-center p-6">
    <div class="max-w-md w-full rounded-2xl border border-border bg-surface p-7 text-center">
      <template v-if="isRedirecting">
        <Icon name="lucide:loader-circle" class="w-8 h-8 text-primary-500 animate-spin mx-auto mb-3" />
        <h1 class="text-lg font-semibold text-heading mb-1">Menyiapkan Course Player</h1>
        <p class="text-sm text-muted">Sedang membuka materi yang paling sesuai untuk progress Anda.</p>
      </template>

      <template v-else-if="error">
        <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500 mx-auto mb-3" />
        <h1 class="text-lg font-semibold text-heading mb-1">Tidak Dapat Membuka Materi</h1>
        <p class="text-sm text-muted mb-4">{{ error }}</p>
        <UButton variant="primary" @click="redirectToLesson">Coba Lagi</UButton>
      </template>
    </div>
  </div>
</template>
