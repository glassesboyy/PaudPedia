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
const hasQuizzes = computed(() => store.flatLessons.some(l => l.type === 'quiz'))

// Load lesson detail on mount + on slug change
async function loadCurrentLesson() {
  if (!activeLessonSlug.value || !store.playerData) return
  if (activeLessonSlug.value === 'completed') return
  await store.loadLessonDetail(activeLessonSlug.value)
}

watch(activeLessonSlug, loadCurrentLesson)
onMounted(loadCurrentLesson)

onBeforeUnmount(() => {
  store.revokePdfBlob()
})
</script>

<template>
  <!-- Congratulation UI -->
  <div v-if="activeLessonSlug === 'completed'" class="max-w-4xl mx-auto rounded-2xl border border-border bg-surface p-8 sm:p-12 text-center m-8 sm:mt-16 shadow-sm">
    <div class="w-24 h-24 bg-success-50 text-success-500 rounded-full flex items-center justify-center mx-auto mb-6">
      <Icon name="lucide:award" class="w-14 h-14" />
    </div>
    <h2 class="text-3xl font-bold text-heading mb-3">Selamat! Anda Telah Menyelesaikan Semua Materi</h2>
    <p class="text-md text-body mb-8 leading-relaxed">
      Anda telah berhasil menyerap dengan tuntas seluruh kurikulum materi pada kursus ini. Terus semangat belajar dan kembangkan ilmu Anda!
    </p>

    <!-- <div v-if="hasQuizzes" class="bg-warning-50 border border-warning-200 rounded-xl p-4 mb-8 text-left flex items-start gap-3">
      <Icon name="lucide:alert-triangle" class="w-5 h-5 text-warning-600 shrink-0 mt-0.5" />
      <div>
        <h4 class="text-sm font-semibold text-warning-800 mb-1">Penting: Persyaratan Sertifikat</h4>
        <p class="text-xs text-warning-700 leading-relaxed">
          Kursus ini memuat <strong>Kuis Evaluasi</strong>. Untuk menerbitkan Sertifikat Kelulusan yang sah di Dashboard, Anda wajib menyelesaikan kuis tersebut dengan predikat <strong>LULUS (skor pencapaian 70+)</strong>.
        </p>
      </div>
    </div> -->

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <NuxtLink
        to="/account/certificates"
        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm"
      >
        <Icon name="lucide:award" class="w-4 h-4" />
        Sertifikat Saya
      </NuxtLink>
      <NuxtLink
        to="/account/courses"
        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-border text-sm font-semibold text-body hover:bg-surface-sunken hover:text-heading transition-colors"
      >
        <Icon name="lucide:book-open" class="w-4 h-4" />
        Kembali ke Beranda Belajar
      </NuxtLink>
    </div>
  </div>

  <div v-else-if="activeLessonSummary" class="max-w-5xl mx-auto p-4 md:p-6 lg:p-8 space-y-4">
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

      <div v-else-if="store.lessonDetail?.type === 'pdf'" class="h-[72vh] min-h-[26rem] bg-surface-sunken relative overflow-hidden">
        <div v-if="store.isPdfLoading" class="absolute inset-0 flex items-center justify-center z-10 bg-surface-sunken">
          <div class="text-center">
            <Icon name="lucide:loader-circle" class="w-7 h-7 text-primary-500 animate-spin mx-auto mb-2" />
            <p class="text-sm text-muted">Memuat dokumen PDF...</p>
          </div>
        </div>

        <div v-else-if="store.pdfError" class="absolute inset-0 flex items-center justify-center p-8 text-center z-10 bg-surface-sunken">
          <div class="bg-surface p-6 rounded-2xl shadow-sm border border-border">
            <Icon name="lucide:file-warning" class="w-8 h-8 text-warning-500 mx-auto mb-2" />
            <p class="text-sm text-muted mb-4">{{ store.pdfError }}</p>
            <UButton variant="outline" @click="store.loadPdfBlob(activeLessonSummary.id)">Muat ulang PDF</UButton>
          </div>
        </div>

        <PdfViewer
          v-else-if="store.pdfBlobUrl"
          :pdf-url="store.pdfBlobUrl"
        />
      </div>

      <div v-else-if="store.lessonDetail?.type === 'text'" class="p-6 md:p-8">
        <article class="prose prose-sm md:prose-base max-w-none text-body" v-html="store.textLessonHtml" />
      </div>

      <QuizViewer
        v-else-if="store.quizDetail"
        :quiz="store.quizDetail"
        @submitted="store.loadPlayerData({ quiet: true }).then(() => store.loadLessonDetail(route.params.lessonSlug as string, { forceRefresh: true, quiet: true }))"
      />

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
            v-if="activeLessonSummary?.type !== 'quiz'"
            variant="primary"
            :disabled="!activeLessonSummary || activeLessonSummary.is_completed || store.isMarkingComplete"
            :loading="store.isMarkingComplete"
            @click="store.markAsComplete(activeLessonSlug)"
          >
            <Icon name="lucide:check-circle-2" class="w-4 h-4 mr-1.5" />
            {{ activeLessonSummary?.is_completed ? 'Sudah Selesai' : 'Tandai Selesai' }}
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
          v-if="nextLesson"
          :to="`/learn/${store.courseSlug}/${nextLesson.slug}`"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium transition-all bg-primary-500 text-white hover:bg-primary-600 shadow-sm"
        >
          <span class="hidden sm:inline truncate max-w-[10rem]">{{ nextLesson.title || 'Next' }}</span>
          <span class="sm:hidden">Next</span>
          <Icon name="lucide:chevron-right" class="w-3.5 h-3.5" />
        </NuxtLink>
        
        <NuxtLink
          v-else-if="store.playerData?.progress.progress_percentage === 100 || activeLessonSummary?.is_completed"
          :to="`/learn/${store.courseSlug}/completed`"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium transition-all bg-success-500 text-white hover:bg-success-600 shadow-sm"
        >
          <span>Selesaikan Kursus</span>
          <Icon name="lucide:flag" class="w-3.5 h-3.5" />
        </NuxtLink>
        <span
          v-else
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium bg-muted/10 text-muted/40 pointer-events-none"
        >
          Selesaikan Kursus
          <Icon name="lucide:flag" class="w-3.5 h-3.5" />
        </span>
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
