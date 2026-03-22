import { defineStore } from 'pinia'
import DOMPurify from 'isomorphic-dompurify'
import { lmsService } from '~~/services'
import type { LmsCoursePlayerData, LmsLessonDetail, LmsLessonSummary } from '~~/types'

export const useLmsStore = defineStore('lms', () => {
  // ─── State ────────────────────────────────────────────────
  const playerData = ref<LmsCoursePlayerData | null>(null)
  const lessonDetail = shallowRef<LmsLessonDetail | null>(null)

  const isCourseLoading = ref(true)
  const isLessonLoading = ref(false)
  const isPdfLoading = ref(false)
  const isMarkingComplete = ref(false)
  const isDownloadingCertificate = ref(false)

  const courseError = ref('')
  const lessonError = ref('')
  const pdfError = ref('')

  const pdfBlobUrl = ref<string | null>(null)

  // Sidebar UI state
  const isSidebarOpen = ref(false)
  const expandedModules = ref<Set<number>>(new Set())

  // Lesson cache for performance
  const lessonCache = new Map<string, LmsLessonDetail>()

  // Current course slug (set by parent page)
  const courseSlug = ref('')

  // ─── Lookups ──────────────────────────────────────────────
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

  // ─── Getters ──────────────────────────────────────────────
  const flatLessons = computed<LmsLessonSummary[]>(() => {
    if (!playerData.value) return []
    return playerData.value.course.modules.flatMap(module => module.lessons)
  })

  const progressWidth = computed(() => {
    const percentage = playerData.value?.progress.progress_percentage || 0
    return `${Math.min(percentage, 100)}%`
  })

  const isCourseCompleted = computed(
    () => (playerData.value?.progress.progress_percentage || 0) >= 100,
  )

  const certificateDownloadUrl = computed(
    () => playerData.value?.certificate.download_url || null,
  )

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
        if (url.pathname.startsWith('/embed/')) return rawUrl
        const videoId = url.searchParams.get('v')
        if (videoId) return `https://www.youtube.com/embed/${videoId}`
      }
    } catch {
      // Fallback
    }

    return rawUrl
  })

  // ─── Lesson navigation helpers ────────────────────────────
  function getActiveLessonIndex(slug: string): number {
    return flatLessons.value.findIndex(l => l.slug === slug)
  }

  function getActiveLessonSummary(slug: string): LmsLessonSummary | null {
    return flatLessons.value.find(l => l.slug === slug) || null
  }

  function getPrevLesson(slug: string): LmsLessonSummary | null {
    const idx = getActiveLessonIndex(slug)
    if (idx <= 0) return null
    return flatLessons.value[idx - 1] ?? null
  }

  function getNextLesson(slug: string): LmsLessonSummary | null {
    const idx = getActiveLessonIndex(slug)
    if (idx < 0 || idx >= flatLessons.value.length - 1) return null
    return flatLessons.value[idx + 1] ?? null
  }

  // ─── Sidebar actions ──────────────────────────────────────
  function toggleModule(moduleId: number) {
    if (expandedModules.value.has(moduleId)) {
      expandedModules.value.delete(moduleId)
    } else {
      expandedModules.value.add(moduleId)
    }
  }

  function expandModuleContainingLesson(lessonSlug: string) {
    if (!playerData.value) return
    for (const module of playerData.value.course.modules) {
      for (const lesson of module.lessons) {
        if (lesson.slug === lessonSlug) {
          expandedModules.value.add(module.id)
          return
        }
      }
    }
  }

  // ─── LocalStorage ─────────────────────────────────────────
  const storageKey = computed(() => `lms:last-lesson:${courseSlug.value}`)

  function setLastLesson(slug: string) {
    if (!import.meta.client) return
    localStorage.setItem(storageKey.value, slug)
  }

  function getLastLesson(): string | null {
    if (!import.meta.client) return null
    return localStorage.getItem(storageKey.value)
  }

  // ─── State update helpers ─────────────────────────────────
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

  // ─── Data loading ─────────────────────────────────────────
  async function loadPdfBlob(lessonId: number) {
    revokePdfBlob()
    isPdfLoading.value = true
    pdfError.value = ''

    try {
      const blob = await lmsService.getLessonPdfBlob(courseSlug.value, lessonId)
      pdfBlobUrl.value = URL.createObjectURL(blob)
    } catch {
      pdfError.value = 'Gagal memuat file PDF. Silakan coba lagi.'
    } finally {
      isPdfLoading.value = false
    }
  }

  async function loadLessonDetail(lessonSlug: string) {
    lessonError.value = ''
    const lessonId = Number(lessonSlug)

    // Check cache first
    const cached = lessonCache.get(lessonSlug)
    if (cached) {
      lessonDetail.value = cached

      if (cached.type === 'pdf') {
        await loadPdfBlob(lessonId)
      } else {
        revokePdfBlob()
      }

      setLastLesson(lessonSlug)
      expandModuleContainingLesson(lessonSlug)
      schedulePrefetch(lessonSlug)
      return
    }

    isLessonLoading.value = true

    try {
      const res = await lmsService.getLessonDetail(courseSlug.value, lessonId)
      lessonDetail.value = res.data
      lessonCache.set(lessonSlug, res.data)

      if (res.data.type === 'pdf') {
        await loadPdfBlob(lessonId)
      } else {
        revokePdfBlob()
      }

      setLastLesson(lessonSlug)
      expandModuleContainingLesson(lessonSlug)
      schedulePrefetch(lessonSlug)
    } catch {
      lessonError.value = 'Gagal memuat detail materi.'
    } finally {
      isLessonLoading.value = false
    }
  }

  async function loadPlayerData() {
    isCourseLoading.value = true
    courseError.value = ''

    try {
      const res = await lmsService.getCoursePlayer(courseSlug.value)
      playerData.value = res.data

      // Expand all modules initially
      for (const module of res.data.course.modules) {
        expandedModules.value.add(module.id)
      }
    } catch {
      courseError.value = 'Gagal memuat data kursus LMS.'
    } finally {
      isCourseLoading.value = false
    }
  }

  // ─── Prefetching ──────────────────────────────────────────
  let prefetchTimer: ReturnType<typeof setTimeout> | null = null

  function schedulePrefetch(currentSlug: string) {
    if (prefetchTimer) clearTimeout(prefetchTimer)

    prefetchTimer = setTimeout(async () => {
      const next = getNextLesson(currentSlug)
      if (!next || lessonCache.has(next.slug)) return

      try {
        const res = await lmsService.getLessonDetail(courseSlug.value, Number(next.slug))
        lessonCache.set(next.slug, res.data)
      } catch {
        // Non-critical
      }
    }, 1500)
  }

  // ─── Actions ──────────────────────────────────────────────
  async function markAsComplete(activeLessonSlug: string) {
    const summary = getActiveLessonSummary(activeLessonSlug)
    if (!summary || summary.is_completed || isMarkingComplete.value) return

    isMarkingComplete.value = true

    try {
      const res = await lmsService.markLessonComplete(courseSlug.value, summary.id)
      updateLessonCompleted(res.data.lesson_id)

      if (playerData.value) {
        playerData.value.progress = res.data.progress
        playerData.value.certificate = res.data.certificate
      }

      // Invalidate cache
      lessonCache.delete(activeLessonSlug)
    } catch {
      lessonError.value = 'Gagal menandai materi sebagai selesai.'
    } finally {
      isMarkingComplete.value = false
    }
  }

  async function downloadCertificate() {
    if (isDownloadingCertificate.value || !courseSlug.value) return
    isDownloadingCertificate.value = true

    try {
      const blob = await lmsService.downloadCertificateBlob(courseSlug.value)
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `Sertifikat - ${playerData.value?.course.title || 'Kursus'}.pdf`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      URL.revokeObjectURL(url)
    } catch {
      lessonError.value = 'Gagal mengunduh sertifikat.'
    } finally {
      isDownloadingCertificate.value = false
    }
  }

  // ─── Cleanup ──────────────────────────────────────────────
  function cleanup() {
    revokePdfBlob()
    if (prefetchTimer) clearTimeout(prefetchTimer)
  }

  function $reset() {
    cleanup()
    playerData.value = null
    lessonDetail.value = null
    isCourseLoading.value = true
    isLessonLoading.value = false
    courseError.value = ''
    lessonError.value = ''
    expandedModules.value.clear()
    lessonCache.clear()
    courseSlug.value = ''
    isSidebarOpen.value = false
  }

  return {
    // State
    playerData,
    lessonDetail,
    isCourseLoading,
    isLessonLoading,
    isPdfLoading,
    isMarkingComplete,
    isDownloadingCertificate,
    courseError,
    lessonError,
    pdfError,
    pdfBlobUrl,
    isSidebarOpen,
    expandedModules,
    courseSlug,

    // Lookups
    lessonTypeIcon,
    lessonTypeLabel,

    // Getters
    flatLessons,
    progressWidth,
    isCourseCompleted,
    certificateDownloadUrl,
    textLessonHtml,
    youtubeEmbedUrl,

    // Helpers
    getActiveLessonIndex,
    getActiveLessonSummary,
    getPrevLesson,
    getNextLesson,
    getLastLesson,
    toggleModule,
    expandModuleContainingLesson,

    // Actions
    loadPlayerData,
    loadLessonDetail,
    loadPdfBlob,
    markAsComplete,
    downloadCertificate,
    revokePdfBlob,
    cleanup,
    $reset,
  }
})
