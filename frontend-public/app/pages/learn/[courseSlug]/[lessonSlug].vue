<script setup lang="ts">
/**
 * LMS Lesson Player
 * Route: /learn/:courseSlug/:lessonSlug
 */
import DOMPurify from 'isomorphic-dompurify'
import { lmsService } from '~~/services'
import type { LmsCoursePlayerData, LmsLessonDetail, LmsLessonSummary } from '~~/types'

definePageMeta({
  layout: 'lms',
  middleware: ['auth'],
})

const route = useRoute()
const courseSlug = computed(() => route.params.courseSlug as string)

const playerData = ref<LmsCoursePlayerData | null>(null)
const lessonDetail = ref<LmsLessonDetail | null>(null)

const isCourseLoading = ref(true)
const isLessonLoading = ref(false)
const isPdfLoading = ref(false)
const isMarkingComplete = ref(false)
const isGeneratingCertificate = ref(false)

const courseError = ref('')
const lessonError = ref('')
const pdfError = ref('')

const pdfBlobUrl = ref<string | null>(null)

const lessonTypeIcon: Record<string, string> = {
  video: 'lucide:play-circle',
  pdf: 'lucide:file-text',
  text: 'lucide:file-pen-line',
}

const lessonTypeLabel: Record<string, string> = {
  video: 'Video',
  pdf: 'PDF',
  text: 'Artikel',
}

const flatLessons = computed<LmsLessonSummary[]>(() => {
  if (!playerData.value) return []
  return playerData.value.course.modules.flatMap(module => module.lessons)
})

const activeLessonSlug = computed(() => String(route.params.lessonSlug || ''))

const activeLessonSummary = computed(() => {
  return flatLessons.value.find(lesson => lesson.slug === activeLessonSlug.value) || null
})

const progressWidth = computed(() => {
  const percentage = playerData.value?.progress.progress_percentage || 0
  return `${Math.min(percentage, 100)}%`
})

const isCourseCompleted = computed(() => (playerData.value?.progress.progress_percentage || 0) >= 100)

const certificateDownloadUrl = computed(() => playerData.value?.certificate.download_url || null)

const textLessonHtml = computed(() => {
  if (!lessonDetail.value?.text_content) return ''
  return DOMPurify.sanitize(lessonDetail.value.text_content)
})

const youtubeEmbedUrl = computed(() => {
  const rawUrl = lessonDetail.value?.video_url
  if (!rawUrl) return ''

  try {
    const url = new URL(rawUrl)

    if (url.hostname.includes('youtu.be')) {
      const id = url.pathname.replace('/', '')
      return `https://www.youtube.com/embed/${id}`
    }

    if (url.hostname.includes('youtube.com')) {
      if (url.pathname.startsWith('/embed/')) {
        return rawUrl
      }

      const videoId = url.searchParams.get('v')
      if (videoId) {
        return `https://www.youtube.com/embed/${videoId}`
      }
    }
  }
  catch {
    // Fallback below
  }

  return rawUrl
})

const storageKey = computed(() => `lms:last-lesson:${courseSlug.value}`)

function setLastLesson(slug: string) {
  if (!import.meta.client) return
  localStorage.setItem(storageKey.value, slug)
}

function getLastLesson(): string | null {
  if (!import.meta.client) return null
  return localStorage.getItem(storageKey.value)
}

function updateLessonCompleted(lessonId: number) {
  if (!playerData.value) return

  playerData.value.course.modules = playerData.value.course.modules.map((module) => {
    return {
      ...module,
      lessons: module.lessons.map((lesson) => {
        if (lesson.id !== lessonId) return lesson
        return { ...lesson, is_completed: true }
      }),
    }
  })
}

function revokePdfBlob() {
  if (pdfBlobUrl.value) {
    URL.revokeObjectURL(pdfBlobUrl.value)
    pdfBlobUrl.value = null
  }
}

async function loadPdfBlob(lessonId: number) {
  revokePdfBlob()
  isPdfLoading.value = true
  pdfError.value = ''

  try {
    const blob = await lmsService.getLessonPdfBlob(courseSlug.value, lessonId)
    pdfBlobUrl.value = URL.createObjectURL(blob)
  }
  catch {
    pdfError.value = 'Gagal memuat file PDF. Silakan coba lagi.'
  }
  finally {
    isPdfLoading.value = false
  }
}

async function loadLessonDetail(lessonSlug: string) {
  lessonError.value = ''
  isLessonLoading.value = true

  const lessonId = Number(lessonSlug)

  try {
    const res = await lmsService.getLessonDetail(courseSlug.value, lessonId)
    lessonDetail.value = res.data

    if (res.data.type === 'pdf') {
      await loadPdfBlob(lessonId)
    }
    else {
      revokePdfBlob()
    }

    setLastLesson(lessonSlug)
  }
  catch {
    lessonError.value = 'Gagal memuat detail materi.'
  }
  finally {
    isLessonLoading.value = false
  }
}

async function loadPlayerData() {
  isCourseLoading.value = true
  courseError.value = ''

  try {
    const res = await lmsService.getCoursePlayer(courseSlug.value)
    playerData.value = res.data

    const currentSlug = String(route.params.lessonSlug || '')
    const currentExists = flatLessons.value.some(lesson => lesson.slug === currentSlug)
    const preferredSlug = currentExists
      ? currentSlug
      : getLastLesson() || playerData.value.next_lesson_slug || flatLessons.value[0]?.slug

    if (!preferredSlug) {
      lessonDetail.value = null
      return
    }

    if (currentSlug !== preferredSlug) {
      await navigateTo(`/learn/${courseSlug.value}/${preferredSlug}`, { replace: true })
      return
    }

    await loadLessonDetail(preferredSlug)
  }
  catch {
    courseError.value = 'Gagal memuat data kursus LMS.'
  }
  finally {
    isCourseLoading.value = false
  }
}

async function onSelectLesson(lesson: LmsLessonSummary) {
  if (lesson.slug === activeLessonSlug.value) return
  await navigateTo(`/learn/${courseSlug.value}/${lesson.slug}`)
}

async function markAsComplete() {
  if (!activeLessonSummary.value || activeLessonSummary.value.is_completed || isMarkingComplete.value) return

  isMarkingComplete.value = true

  try {
    const res = await lmsService.markLessonComplete(courseSlug.value, activeLessonSummary.value.id)
    updateLessonCompleted(res.data.lesson_id)

    if (playerData.value) {
      playerData.value.progress = res.data.progress
      playerData.value.certificate = res.data.certificate
    }
  }
  catch {
    lessonError.value = 'Gagal menandai materi sebagai selesai.'
  }
  finally {
    isMarkingComplete.value = false
  }
}

async function generateCertificate() {
  if (isGeneratingCertificate.value) return

  isGeneratingCertificate.value = true

  try {
    const res = await lmsService.generateCertificate(courseSlug.value)
    if (playerData.value) {
      playerData.value.certificate = res.data
    }
  }
  catch {
    lessonError.value = 'Gagal menyiapkan sertifikat.'
  }
  finally {
    isGeneratingCertificate.value = false
  }
}

watch(
  () => route.params.lessonSlug,
  async (slug) => {
    if (!slug || !playerData.value) return
    await loadLessonDetail(String(slug))
  },
)

onMounted(loadPlayerData)
onBeforeUnmount(() => {
  revokePdfBlob()
})

useSeo({ title: 'Belajar' })
</script>

<template>
  <div class="min-h-[calc(100vh-3.5rem)]">
    <div v-if="isCourseLoading" class="grid grid-cols-1 lg:grid-cols-[22rem_1fr] h-full">
      <aside class="border-r border-border bg-surface p-5 space-y-3">
        <USkeleton class="h-6 w-2/3" />
        <USkeleton class="h-2 w-full" />
        <USkeleton class="h-2 w-1/2" />
        <div class="space-y-2 pt-2">
          <USkeleton v-for="i in 8" :key="i" class="h-10 w-full" />
        </div>
      </aside>
      <section class="p-6 lg:p-8 space-y-4">
        <USkeleton class="h-8 w-1/2" />
        <USkeleton class="h-[24rem] w-full rounded-2xl" />
      </section>
    </div>

    <div v-else-if="courseError" class="p-6 lg:p-10">
      <div class="max-w-xl mx-auto rounded-2xl border border-danger-200 bg-danger-50 p-8 text-center">
        <Icon name="lucide:wifi-off" class="w-8 h-8 text-danger-500 mx-auto mb-3" />
        <h2 class="text-lg font-semibold text-heading mb-2">Gagal Memuat LMS</h2>
        <p class="text-sm text-muted mb-5">{{ courseError }}</p>
        <UButton variant="primary" @click="loadPlayerData">
          <Icon name="lucide:refresh-cw" class="w-4 h-4 mr-2" />
          Muat Ulang
        </UButton>
      </div>
    </div>

    <div v-else-if="playerData" class="grid grid-cols-1 lg:grid-cols-[22rem_1fr] h-full">
      <aside class="border-r border-border bg-surface flex flex-col max-h-[calc(100vh-3.5rem)]">
        <div class="p-5 border-b border-border">
          <p class="text-xs font-semibold uppercase tracking-wide text-primary-600 mb-2">Course Player</p>
          <h1 class="text-base font-bold text-heading line-clamp-2 mb-4">{{ playerData.course.title }}</h1>

          <div class="space-y-2">
            <div class="flex items-center justify-between text-xs text-muted">
              <span>{{ playerData.progress.completed_lessons }}/{{ playerData.progress.total_lessons }} selesai</span>
              <span class="font-semibold text-primary-600">{{ playerData.progress.progress_percentage }}%</span>
            </div>
            <div class="h-2 rounded-full bg-surface-sunken overflow-hidden">
              <div class="h-full rounded-full bg-primary-500 transition-all duration-500" :style="{ width: progressWidth }" />
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          <div v-for="module in playerData.course.modules" :key="module.id" class="space-y-2">
            <div class="px-2">
              <p class="text-xs font-semibold text-heading">{{ module.title }}</p>
              <p v-if="module.description" class="text-[11px] text-muted line-clamp-2">{{ module.description }}</p>
            </div>

            <button
              v-for="lesson in module.lessons"
              :key="lesson.id"
              type="button"
              class="w-full rounded-xl border px-3 py-2.5 text-left transition-all duration-200"
              :class="lesson.slug === activeLessonSlug
                ? 'border-primary-300 bg-primary-50 shadow-sm'
                : 'border-border bg-surface hover:border-primary-200 hover:bg-primary-50/40'"
              @click="onSelectLesson(lesson)"
            >
              <div class="flex items-start gap-2.5">
                <div class="mt-0.5">
                  <Icon
                    :name="lesson.is_completed ? 'lucide:check-circle-2' : (lessonTypeIcon[lesson.type || ''] || 'lucide:file')"
                    class="w-4 h-4"
                    :class="lesson.is_completed ? 'text-success-500' : 'text-muted'"
                  />
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-heading truncate">{{ lesson.title }}</p>
                  <p class="text-[11px] text-muted mt-0.5">
                    {{ lessonTypeLabel[lesson.type || ''] || 'Materi' }}
                    <span v-if="lesson.duration_minutes"> · {{ lesson.duration_minutes }} menit</span>
                  </p>
                </div>
              </div>
            </button>
          </div>
        </div>
      </aside>

      <section class="bg-surface-sunken/40 p-4 md:p-6 lg:p-8 overflow-y-auto max-h-[calc(100vh-3.5rem)]">
        <div v-if="activeLessonSummary" class="max-w-5xl mx-auto space-y-5">
          <header class="rounded-2xl border border-border bg-surface px-5 py-4">
            <div class="flex flex-wrap items-center gap-2 mb-2">
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-primary-100 text-primary-700">
                <Icon :name="lessonTypeIcon[activeLessonSummary.type || ''] || 'lucide:file'" class="w-3.5 h-3.5" />
                {{ lessonTypeLabel[activeLessonSummary.type || ''] || 'Materi' }}
              </span>
              <span
                v-if="activeLessonSummary.is_completed"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-success-100 text-success-700"
              >
                <Icon name="lucide:check" class="w-3.5 h-3.5" />
                Selesai
              </span>
            </div>

            <h2 class="text-xl md:text-2xl font-bold text-heading mb-1">{{ activeLessonSummary.title }}</h2>
            <p class="text-sm text-muted">Fokus belajar pada materi ini, lalu tandai selesai untuk update progres otomatis.</p>
          </header>

          <div class="rounded-2xl border border-border bg-surface overflow-hidden min-h-[22rem]">
            <div v-if="isLessonLoading" class="p-5 space-y-3">
              <USkeleton class="h-6 w-1/3" />
              <USkeleton class="h-[20rem] w-full rounded-xl" />
            </div>

            <div v-else-if="lessonError" class="p-8 text-center">
              <Icon name="lucide:alert-circle" class="w-8 h-8 text-danger-500 mx-auto mb-2" />
              <p class="text-sm text-muted mb-4">{{ lessonError }}</p>
              <UButton variant="outline" @click="loadLessonDetail(activeLessonSlug)">Coba lagi</UButton>
            </div>

            <div v-else-if="lessonDetail?.type === 'video'" class="relative w-full pb-[56.25%] bg-black">
              <iframe
                :src="youtubeEmbedUrl"
                class="absolute inset-0 w-full h-full"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy"
              />
            </div>

            <div v-else-if="lessonDetail?.type === 'pdf'" class="h-[72vh] min-h-[26rem] bg-surface-sunken">
              <div v-if="isPdfLoading" class="h-full flex items-center justify-center">
                <div class="text-center">
                  <Icon name="lucide:loader-circle" class="w-7 h-7 text-primary-500 animate-spin mx-auto mb-2" />
                  <p class="text-sm text-muted">Memuat dokumen PDF...</p>
                </div>
              </div>

              <div v-else-if="pdfError" class="h-full flex items-center justify-center p-8 text-center">
                <div>
                  <Icon name="lucide:file-warning" class="w-8 h-8 text-warning-500 mx-auto mb-2" />
                  <p class="text-sm text-muted mb-4">{{ pdfError }}</p>
                  <UButton variant="outline" @click="loadPdfBlob(activeLessonSummary.id)">Muat ulang PDF</UButton>
                </div>
              </div>

              <iframe
                v-else-if="pdfBlobUrl"
                :src="pdfBlobUrl"
                class="w-full h-full"
                title="PDF Viewer"
              />
            </div>

            <div v-else-if="lessonDetail?.type === 'text'" class="p-6 md:p-8">
              <article class="prose prose-sm md:prose-base max-w-none text-body" v-html="textLessonHtml" />
            </div>

            <div v-else class="p-8 text-center text-muted">
              Tipe materi belum didukung.
            </div>
          </div>

          <footer class="rounded-2xl border border-border bg-surface p-4 md:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <p class="text-sm text-muted">
                Progres kursus Anda: <span class="font-semibold text-heading">{{ playerData.progress.progress_percentage }}%</span>
              </p>

              <div class="flex flex-wrap gap-2">
                <UButton
                  variant="primary"
                  :disabled="!activeLessonSummary || activeLessonSummary.is_completed || isMarkingComplete"
                  :loading="isMarkingComplete"
                  @click="markAsComplete"
                >
                  <Icon name="lucide:check-circle-2" class="w-4 h-4 mr-1.5" />
                  {{ activeLessonSummary?.is_completed ? 'Sudah Selesai' : 'Mark as Complete' }}
                </UButton>

                <UButton
                  v-if="isCourseCompleted"
                  variant="outline"
                  :loading="isGeneratingCertificate"
                  @click="generateCertificate"
                >
                  <Icon name="lucide:award" class="w-4 h-4 mr-1.5" />
                  Generate Sertifikat
                </UButton>

                <a v-if="certificateDownloadUrl" :href="certificateDownloadUrl" target="_blank" rel="noopener">
                  <UButton variant="secondary">
                    <Icon name="lucide:download" class="w-4 h-4 mr-1.5" />
                    Download Sertifikat
                  </UButton>
                </a>
              </div>
            </div>
          </footer>
        </div>

        <div v-else class="max-w-xl mx-auto rounded-2xl border border-border bg-surface p-8 text-center">
          <Icon name="lucide:book-x" class="w-8 h-8 text-muted mx-auto mb-2" />
          <h2 class="text-lg font-semibold text-heading mb-2">Materi Belum Tersedia</h2>
          <p class="text-sm text-muted">Kursus ini belum memiliki materi untuk ditampilkan.</p>
        </div>
      </section>
    </div>
  </div>
</template>
