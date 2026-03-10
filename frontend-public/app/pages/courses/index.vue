<script setup lang="ts">
/**
 * Course Listing Page
 * Route: /courses
 *
 * Full listing with search, category/level filters, and pagination.
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Course, CourseListParams } from '~~/types'

useSeo({
  title: 'Kursus',
  description: 'Temukan kursus terbaik untuk pendidikan anak usia dini di PaudPedia.',
})

// ─── State ───
const courses = ref<Course[]>([])
const meta = ref<PaginationMeta | null>(null)
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedLevel = ref('')
const currentPage = ref(1)

// ─── Fetch ───
async function fetchCourses() {
  loading.value = true
  error.value = false
  try {
    const { courseService } = await import('~~/services')
    const params: CourseListParams = {
      page: currentPage.value,
      per_page: 12,
    }
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (selectedCategory.value) params.category = selectedCategory.value
    if (selectedLevel.value) params.level = selectedLevel.value

    const res = await courseService.getList(params)
    if (res.success) {
      courses.value = res.data
      meta.value = res.meta
    }
    else {
      error.value = true
    }
  }
  catch {
    error.value = true
  }
  finally {
    loading.value = false
  }
}

// ─── Watchers ───
function resetAndFetch() {
  currentPage.value = 1
  fetchCourses()
}

function goToPage(page: number) {
  currentPage.value = page
  fetchCourses()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch(selectedCategory, resetAndFetch)
watch(selectedLevel, resetAndFetch)

onMounted(fetchCourses)

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasCourses = computed(() => courses.value.length > 0)
const hasActiveFilter = computed(() => !!searchQuery.value || !!selectedCategory.value || !!selectedLevel.value)

const pageNumbers = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  const pages: number[] = []
  const delta = 2
  for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
    pages.push(i)
  }
  return pages
})

// ─── Filter options ───
const categories = [
  { label: 'Semua', value: '' },
  { label: 'Kurikulum', value: 'kurikulum' },
  { label: 'Metode Belajar', value: 'metode-belajar' },
  { label: 'Perkembangan Anak', value: 'perkembangan-anak' },
  { label: 'Media Belajar', value: 'media-belajar' },
  { label: 'Manajemen PAUD', value: 'manajemen-paud' },
]

const levels = [
  { label: 'Semua Level', value: '' },
  { label: 'Pemula', value: 'beginner' },
  { label: 'Menengah', value: 'intermediate' },
  { label: 'Lanjutan', value: 'advanced' },
]

function clearFilters() {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedLevel.value = ''
}
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-primary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Kursus</h1>
        <p class="mt-4 text-base text-body max-w-2xl mx-auto leading-relaxed">
          Temukan kursus terbaik untuk meningkatkan kualitas pendidikan anak usia dini.
        </p>
      </div>
    </section>

    <!-- Filters -->
    <section class="bg-surface border-b border-border-muted sticky top-0 z-sticky">
      <div class="container py-4">
        <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
          <!-- Search -->
          <div class="relative flex-1 max-w-md">
            <Icon name="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari kursus..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors"
            />
          </div>

          <!-- Category Filter -->
          <div class="flex flex-wrap gap-2">
            <button
              v-for="cat in categories"
              :key="cat.value"
              type="button"
              class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-colors"
              :class="
                selectedCategory === cat.value
                  ? 'bg-primary-500 text-white border-primary-500'
                  : 'bg-surface border-border text-body hover:border-primary-300 hover:text-primary-600'
              "
              @click="selectedCategory = cat.value"
            >
              {{ cat.label }}
            </button>
          </div>

          <!-- Level Filter -->
          <select
            v-model="selectedLevel"
            class="px-3 py-2 rounded-xl border border-border bg-surface text-sm text-heading focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors"
          >
            <option v-for="lvl in levels" :key="lvl.value" :value="lvl.value">
              {{ lvl.label }}
            </option>
          </select>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="bg-surface">
      <div class="container py-10 sm:py-14">
        <!-- Loading -->
        <SkeletonCardGrid v-if="loading" :count="6" :columns="3" variant="media" />

        <!-- Error -->
        <div v-else-if="error" class="text-center py-14">
          <Icon name="lucide:alert-triangle" class="w-12 h-12 text-warning-500 mx-auto mb-4" />
          <p class="text-sm text-body mb-4">Gagal memuat daftar kursus.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors"
            @click="fetchCourses"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty -->
        <div v-else-if="!hasCourses" class="text-center py-14">
          <Icon name="lucide:book-open" class="w-12 h-12 text-muted mx-auto mb-4" />
          <h3 class="text-base font-semibold text-heading mb-2">Belum Ada Kursus</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilter">
              Tidak ditemukan kursus yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline ml-1" @click="clearFilters">
                Reset filter
              </button>
            </template>
            <template v-else>
              Kursus akan segera hadir. Nantikan konten terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Grid -->
        <template v-else>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <CourseCard
              v-for="course in courses"
              :key="course.id"
              :course="course"
            />
          </div>

          <!-- Pagination -->
          <nav v-if="totalPages > 1" class="flex items-center justify-center gap-2 mt-10">
            <button
              type="button"
              :disabled="currentPage <= 1"
              class="p-2 rounded-lg border border-border text-muted hover:text-heading hover:border-primary-300 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
              @click="goToPage(currentPage - 1)"
            >
              <Icon name="lucide:chevron-left" class="w-4 h-4" />
            </button>
            <button
              v-for="page in pageNumbers"
              :key="page"
              type="button"
              class="w-9 h-9 rounded-lg text-sm font-medium transition-colors"
              :class="
                page === currentPage
                  ? 'bg-primary-500 text-white'
                  : 'border border-border text-body hover:border-primary-300 hover:text-primary-600'
              "
              @click="goToPage(page)"
            >
              {{ page }}
            </button>
            <button
              type="button"
              :disabled="currentPage >= totalPages"
              class="p-2 rounded-lg border border-border text-muted hover:text-heading hover:border-primary-300 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
              @click="goToPage(currentPage + 1)"
            >
              <Icon name="lucide:chevron-right" class="w-4 h-4" />
            </button>
          </nav>

          <p v-if="meta" class="text-center text-xs text-muted mt-4">
            Menampilkan {{ courses.length }} dari {{ meta.total }} kursus
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
