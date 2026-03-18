<script setup lang="ts">
/**
 * LMS Course Overview
 * Route: /learn/:courseSlug
 */
import { lmsService } from '~~/services'

definePageMeta({
  layout: 'lms',
  middleware: ['auth'],
})

const route = useRoute()
const courseSlug = route.params.courseSlug as string

const isLoading = ref(true)
const error = ref('')

async function openPreferredLesson() {
  isLoading.value = true
  error.value = ''

  try {
    const res = await lmsService.getCoursePlayer(courseSlug)

    const firstLesson = res.data.course.modules.flatMap(module => module.lessons)[0]
    const targetLessonSlug = res.data.next_lesson_slug || firstLesson?.slug

    if (!targetLessonSlug) {
      error.value = 'Kursus ini belum memiliki materi untuk dipelajari.'
      return
    }

    await navigateTo(`/learn/${courseSlug}/${targetLessonSlug}`, { replace: true })
  }
  catch {
    error.value = 'Gagal memuat data pembelajaran. Silakan coba lagi.'
  }
  finally {
    isLoading.value = false
  }
}

onMounted(openPreferredLesson)

useSeo({ title: 'Belajar' })
</script>

<template>
  <div class="min-h-[calc(100vh-3.5rem)] flex items-center justify-center p-6">
    <div class="max-w-md w-full rounded-2xl border border-border bg-surface p-7 text-center">
      <template v-if="isLoading">
        <Icon name="lucide:loader-circle" class="w-8 h-8 text-primary-500 animate-spin mx-auto mb-3" />
        <h1 class="text-lg font-semibold text-heading mb-1">Menyiapkan Course Player</h1>
        <p class="text-sm text-muted">Sedang membuka materi yang paling sesuai untuk progress Anda.</p>
      </template>

      <template v-else-if="error">
        <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500 mx-auto mb-3" />
        <h1 class="text-lg font-semibold text-heading mb-1">Tidak Dapat Membuka Materi</h1>
        <p class="text-sm text-muted mb-4">{{ error }}</p>
        <UButton variant="primary" @click="openPreferredLesson">Coba Lagi</UButton>
      </template>
    </div>
  </div>
</template>
