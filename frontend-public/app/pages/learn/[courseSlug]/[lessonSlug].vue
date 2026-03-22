<script setup lang="ts">
/**
 * LMS Lesson Content (Child Page)
 * Route: /learn/:courseSlug/:lessonSlug
 *
 * This is rendered inside the parent [courseSlug].vue via <NuxtPage />.
 * Only contains lesson content — sidebar is in the parent and persists.
 */
import { useLmsStore } from '~~/stores/lms'

const route = useRoute()
const store = useLmsStore()

const activeLessonSlug = computed(() => String(route.params.lessonSlug || ''))

const activeLessonSummary = computed(() => store.getActiveLessonSummary(activeLessonSlug.value))
const activeLessonIndex = computed(() => store.getActiveLessonIndex(activeLessonSlug.value))
const prevLesson = computed(() => store.getPrevLesson(activeLessonSlug.value))
const nextLesson = computed(() => store.getNextLesson(activeLessonSlug.value))

// Load lesson detail on mount + on slug change
async function loadCurrentLesson() {
  if (!activeLessonSlug.value || !store.playerData) return
  await store.loadLessonDetail(activeLessonSlug.value)
}

watch(activeLessonSlug, loadCurrentLesson)
onMounted(loadCurrentLesson)

onBeforeUnmount(() => {
  store.revokePdfBlob()
})
</script>

<template>
  <div v-if="activeLessonSummary" class="max-w-5xl mx-auto p-4 md:p-6 lg:p-8 space-y-4">
    <!-- Lesson header -->
    <header class="rounded-2xl border border-border bg-surface px-5 py-4">
      <div class="flex flex-wrap items-center gap-2 mb-2">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-primary-100 text-primary-700">
          <Icon :name="store.lessonTypeIcon[activeLessonSummary.type || ''] || 'lucide:file'" class="w-3.5 h-3.5" />
          {{ store.lessonTypeLabel[activeLessonSummary.type || ''] || 'Materi' }}
        </span>
        <span
          v-if="activeLessonSummary.is_completed"
          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-success-100 text-success-700"
        >
          <Icon name="lucide:check" class="w-3.5 h-3.5" />
          Selesai
        </span>
        <span v-if="store.flatLessons.length > 0" class="text-[11px] text-muted ml-auto">
          {{ activeLessonIndex + 1 }} / {{ store.flatLessons.length }}
        </span>
      </div>

      <h2 class="text-lg md:text-xl font-bold text-heading mb-1 leading-snug">{{ activeLessonSummary.title }}</h2>
      <p class="text-xs text-muted">Fokus belajar pada materi ini, lalu tandai selesai untuk update progres.</p>
    </header>

    <!-- Content area -->
    <div class="rounded-2xl border border-border bg-surface overflow-hidden min-h-[22rem]">
      <!-- Lesson loading skeleton -->
      <div v-if="store.isLessonLoading" class="animate-pulse">
        <div class="aspect-video bg-muted/10" />
        <div class="p-5 space-y-3">
          <div class="h-4 w-1/3 bg-muted/10 rounded" />
          <div class="h-3 w-2/3 bg-muted/10 rounded" />
        </div>
      </div>

      <div v-else-if="store.lessonError" class="p-8 text-center">
        <Icon name="lucide:alert-circle" class="w-8 h-8 text-danger-500 mx-auto mb-2" />
        <p class="text-sm text-muted mb-4">{{ store.lessonError }}</p>
        <UButton variant="outline" @click="loadCurrentLesson">Coba lagi</UButton>
      </div>

      <div v-else-if="store.lessonDetail?.type === 'video'" class="relative w-full pb-[56.25%] bg-black rounded-t-2xl overflow-hidden">
        <iframe
          :src="store.youtubeEmbedUrl"
          class="absolute inset-0 w-full h-full"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
          loading="lazy"
        />
      </div>

      <div v-else-if="store.lessonDetail?.type === 'pdf'" class="h-[72vh] min-h-[26rem] bg-surface-sunken">
        <div v-if="store.isPdfLoading" class="h-full flex items-center justify-center">
          <div class="text-center">
            <Icon name="lucide:loader-circle" class="w-7 h-7 text-primary-500 animate-spin mx-auto mb-2" />
            <p class="text-sm text-muted">Memuat dokumen PDF...</p>
          </div>
        </div>

        <div v-else-if="store.pdfError" class="h-full flex items-center justify-center p-8 text-center">
          <div>
            <Icon name="lucide:file-warning" class="w-8 h-8 text-warning-500 mx-auto mb-2" />
            <p class="text-sm text-muted mb-4">{{ store.pdfError }}</p>
            <UButton variant="outline" @click="store.loadPdfBlob(activeLessonSummary.id)">Muat ulang PDF</UButton>
          </div>
        </div>

        <iframe
          v-else-if="store.pdfBlobUrl"
          :src="store.pdfBlobUrl"
          class="w-full h-full"
          title="PDF Viewer"
        />
      </div>

      <div v-else-if="store.lessonDetail?.type === 'text'" class="p-6 md:p-8">
        <article class="prose prose-sm md:prose-base max-w-none text-body" v-html="store.textLessonHtml" />
      </div>

      <div v-else class="p-8 text-center text-muted">
        Tipe materi belum didukung.
      </div>
    </div>

    <!-- Footer with nav + actions -->
    <footer class="rounded-2xl border border-border bg-surface p-4 md:p-5">
      <!-- Progress + Actions row -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <p class="text-sm text-muted">
          Progres: <span class="font-semibold text-heading">{{ store.playerData?.progress.progress_percentage }}%</span>
        </p>

        <div class="flex flex-wrap gap-2">
          <UButton
            variant="primary"
            :disabled="!activeLessonSummary || activeLessonSummary.is_completed || store.isMarkingComplete"
            :loading="store.isMarkingComplete"
            @click="store.markAsComplete(activeLessonSlug)"
          >
            <Icon name="lucide:check-circle-2" class="w-4 h-4 mr-1.5" />
            {{ activeLessonSummary?.is_completed ? 'Sudah Selesai' : 'Tandai Selesai' }}
          </UButton>

          <UButton
            v-if="store.isCourseCompleted && store.certificateDownloadUrl"
            variant="secondary"
            :loading="store.isDownloadingCertificate"
            @click="store.downloadCertificate()"
          >
            <Icon name="lucide:download" class="w-4 h-4 mr-1.5" />
            Unduh Sertifikat
          </UButton>
        </div>
      </div>

      <!-- Prev / Next navigation -->
      <div class="flex items-center justify-between gap-3 pt-3 border-t border-border-muted">
        <NuxtLink
          :to="prevLesson ? `/learn/${store.courseSlug}/${prevLesson.slug}` : undefined"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium transition-all"
          :class="prevLesson
            ? 'text-body border border-border hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50'
            : 'text-muted/40 pointer-events-none'"
        >
          <Icon name="lucide:chevron-left" class="w-3.5 h-3.5" />
          <span class="hidden sm:inline truncate max-w-[10rem]">{{ prevLesson?.title || 'Prev' }}</span>
          <span class="sm:hidden">Prev</span>
        </NuxtLink>

        <NuxtLink
          :to="nextLesson ? `/learn/${store.courseSlug}/${nextLesson.slug}` : undefined"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium transition-all"
          :class="nextLesson
            ? 'bg-primary-500 text-white hover:bg-primary-600 shadow-sm'
            : 'text-muted/40 pointer-events-none'"
        >
          <span class="hidden sm:inline truncate max-w-[10rem]">{{ nextLesson?.title || 'Next' }}</span>
          <span class="sm:hidden">Next</span>
          <Icon name="lucide:chevron-right" class="w-3.5 h-3.5" />
        </NuxtLink>
      </div>
    </footer>
  </div>

  <!-- No lessons -->
  <div v-else-if="!store.isLessonLoading" class="max-w-xl mx-auto rounded-2xl border border-border bg-surface p-8 text-center m-8">
    <Icon name="lucide:book-x" class="w-8 h-8 text-muted mx-auto mb-2" />
    <h2 class="text-lg font-semibold text-heading mb-2">Materi Belum Tersedia</h2>
    <p class="text-sm text-muted">Kursus ini belum memiliki materi untuk ditampilkan.</p>
  </div>
</template>
