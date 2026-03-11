<script setup lang="ts">
/**
 * Course Listing Page
 * Route: /courses
 *
 * FR-CR-P01: Display course list for guest and user
 * FR-CR-P04: Filter by category, price, and level
 * FR-CR-P05: Search course by keyword
 *
 * UI pattern aligned with products/index.vue and webinars/index.vue for consistency.
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Category, Course, CourseListParams } from '~~/types'

useSeo({
  title: 'Kursus',
  description: 'Temukan kursus terbaik untuk pendidikan anak usia dini di PaudPedia.',
})

// ─── State ───
const courses = ref<Course[]>([])
const meta = ref<PaginationMeta | null>(null)
const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedLevel = ref('')
const selectedPriceRange = ref('')
const selectedSort = ref('')
const currentPage = ref(1)

// ─── Filter options ───
const levelOptions = [
  { label: 'Semua Level', value: '' },
  { label: 'Pemula', value: 'beginner' },
  { label: 'Menengah', value: 'intermediate' },
  { label: 'Lanjutan', value: 'advanced' },
]

const priceRangeOptions = [
  { label: 'Semua Harga', value: '' },
  { label: 'Gratis', value: 'free' },
  { label: '< Rp50.000', value: '0-50000' },
  { label: 'Rp50.000 - Rp100.000', value: '50000-100000' },
  { label: 'Rp100.000 - Rp250.000', value: '100000-250000' },
  { label: '> Rp250.000', value: '250000+' },
]

const sortOptions = [
  { label: 'Terbaru', value: '' },
  { label: 'Harga Terendah', value: 'price-asc' },
  { label: 'Harga Tertinggi', value: 'price-desc' },
  { label: 'Judul A-Z', value: 'title-asc' },
  { label: 'Durasi Terpanjang', value: 'duration_hours-desc' },
]

// ─── Build API params ───
function buildParams(): CourseListParams {
  const params: CourseListParams = {
    page: currentPage.value,
    per_page: 12,
  }
  if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
  if (selectedCategory.value) params.category = selectedCategory.value
  if (selectedLevel.value) params.level = selectedLevel.value

  // Price range
  if (selectedPriceRange.value === 'free') {
    params.free = true
  }
  else if (selectedPriceRange.value) {
    const [min, max] = selectedPriceRange.value.split('-')
    if (min) params.min_price = Number(min)
    if (max && max !== '+') params.max_price = Number(max)
  }

  // Sort
  if (selectedSort.value) {
    const [field, order] = selectedSort.value.split('-')
    params.sort_by = field as CourseListParams['sort_by']
    params.sort_order = order as CourseListParams['sort_order']
  }

  return params
}

// ─── URL sync ───
const router = useRouter()

function syncQueryParams() {
  const query: Record<string, string> = {}
  if (searchQuery.value.trim()) query.search = searchQuery.value.trim()
  if (selectedCategory.value) query.category = selectedCategory.value
  if (selectedLevel.value) query.level = selectedLevel.value
  if (selectedPriceRange.value) query.price = selectedPriceRange.value
  if (selectedSort.value) query.sort = selectedSort.value
  if (currentPage.value > 1) query.page = String(currentPage.value)
  router.replace({ query })
}

// ─── Fetch categories from API ───
async function fetchCategories() {
  try {
    const { categoryService } = await import('~~/services')
    const res = await categoryService.getCourseCategories()
    if (res.success) {
      categories.value = res.data
    }
  }
  catch {
    // Categories are an enhancement; empty list fallback is fine
  }
}

// ─── Fetch courses ───
async function fetchCourses() {
  loading.value = true
  error.value = false
  try {
    const { courseService } = await import('~~/services')
    const params = buildParams()
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

// ─── Actions ───
function resetAndFetch() {
  currentPage.value = 1
  syncQueryParams()
  fetchCourses()
}

function goToPage(page: number) {
  currentPage.value = page
  syncQueryParams()
  fetchCourses()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function clearAllFilters() {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedLevel.value = ''
  selectedPriceRange.value = ''
  selectedSort.value = ''
}

// ─── Watchers ───
let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch([selectedCategory, selectedLevel, selectedPriceRange, selectedSort], resetAndFetch)

onMounted(() => {
  const route = useRoute()
  if (route.query.search) searchQuery.value = route.query.search as string
  if (route.query.category) selectedCategory.value = route.query.category as string
  if (route.query.level) selectedLevel.value = route.query.level as string
  if (route.query.price) selectedPriceRange.value = route.query.price as string
  if (route.query.sort) selectedSort.value = route.query.sort as string
  if (route.query.page) currentPage.value = Number(route.query.page) || 1

  fetchCategories()
  fetchCourses()
})

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasCourses = computed(() => courses.value.length > 0)

const hasActiveFilters = computed(() =>
  searchQuery.value.trim() !== ''
  || selectedCategory.value !== ''
  || selectedLevel.value !== ''
  || selectedPriceRange.value !== ''
  || selectedSort.value !== '',
)

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

const firstPage = computed(() => pageNumbers.value[0] ?? 1)
const lastPage = computed(() => pageNumbers.value[pageNumbers.value.length - 1] ?? 1)
</script>

<template>
  <div>
    <!-- Hero -->
    <PageHero
      badge="Kursus"
      badge-icon="lucide:graduation-cap"
      title="Kursus Online"
      highlight="PAUD"
      description="Tingkatkan kualitas pendidikan anak usia dini dengan kursus dari mentor berpengalaman — mulai dari kurikulum hingga metode pembelajaran."
      variant="gradient"
    />

    <!-- Filters -->
    <section class="bg-surface border-b border-border/50 sticky top-0 z-40">
      <div class="container py-4">
        <div class="flex flex-col gap-3">
          <!-- Row 1: Search + clear filters -->
          <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
              <Icon name="lucide:search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari kursus..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-400 transition-all"
              />
              <button
                v-if="searchQuery"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-heading transition-colors"
                @click="searchQuery = ''"
              >
                <Icon name="lucide:x" class="w-4 h-4" />
              </button>
            </div>

            <!-- Clear all filters -->
            <button
              v-if="hasActiveFilters"
              type="button"
              class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-danger-600 hover:text-danger-700 hover:bg-danger-50 rounded-lg transition-colors"
              @click="clearAllFilters"
            >
              <Icon name="lucide:x-circle" class="w-3.5 h-3.5" />
              Reset Filter
            </button>
          </div>

          <!-- Row 2: Category pills -->
          <div class="flex flex-wrap gap-2">
            <button
              type="button"
              class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-all duration-200"
              :class="
                selectedCategory === ''
                  ? 'bg-primary-500 text-white border-primary-500 shadow-sm'
                  : 'bg-surface border-border text-body hover:border-primary-300 hover:text-primary-600'
              "
              @click="selectedCategory = ''"
            >
              Semua
            </button>
            <button
              v-for="cat in categories"
              :key="cat.id"
              type="button"
              class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-all duration-200"
              :class="
                selectedCategory === cat.slug
                  ? 'bg-primary-500 text-white border-primary-500 shadow-sm'
                  : 'bg-surface border-border text-body hover:border-primary-300 hover:text-primary-600'
              "
              @click="selectedCategory = cat.slug"
            >
              {{ cat.name }}
            </button>
          </div>

          <!-- Row 3: Level / Price / Sort -->
          <div v-if="!loading" class="flex flex-wrap gap-2 items-center">
            <span class="text-[11px] text-muted font-medium mr-1">Filter:</span>

            <!-- Level -->
            <select
              v-model="selectedLevel"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200 bg-surface cursor-pointer appearance-none"
              :class="
                selectedLevel
                  ? 'bg-primary-100 text-primary-700 border-primary-300'
                  : 'border-border/60 text-muted hover:border-primary-200 hover:text-primary-600'
              "
            >
              <option v-for="opt in levelOptions" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>

            <!-- Price range -->
            <select
              v-model="selectedPriceRange"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200 bg-surface cursor-pointer appearance-none"
              :class="
                selectedPriceRange
                  ? 'bg-primary-100 text-primary-700 border-primary-300'
                  : 'border-border/60 text-muted hover:border-primary-200 hover:text-primary-600'
              "
            >
              <option v-for="opt in priceRangeOptions" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>

            <!-- Sort -->
            <select
              v-model="selectedSort"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200 bg-surface cursor-pointer appearance-none"
              :class="
                selectedSort
                  ? 'bg-primary-100 text-primary-700 border-primary-300'
                  : 'border-border/60 text-muted hover:border-primary-200 hover:text-primary-600'
              "
            >
              <option value="">Urutkan</option>
              <option v-for="opt in sortOptions.slice(1)" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="bg-surface">
      <div class="container py-10 sm:py-14">
        <!-- Loading -->
        <SkeletonCardGrid v-if="loading" :count="6" :columns="3" variant="media" />

        <!-- Error -->
        <div v-else-if="error" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-warning-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Gagal Memuat Kursus</h3>
          <p class="text-sm text-body mb-6 max-w-sm mx-auto">Terjadi kesalahan saat memuat daftar kursus. Silakan coba lagi.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
            @click="fetchCourses"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!hasCourses" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:book-open" class="w-8 h-8 text-primary-400" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Belum Ada Kursus</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilters">
              Tidak ditemukan kursus yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline font-medium ml-1" @click="clearAllFilters">
                Reset filter
              </button>
            </template>
            <template v-else>
              Kursus akan segera hadir. Nantikan konten terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Course Grid -->
        <template v-else>
          <!-- Results summary -->
          <div v-if="meta && hasActiveFilters" class="mb-6 flex items-center gap-2 text-sm text-muted">
            <Icon name="lucide:filter" class="w-4 h-4" />
            <span>Menampilkan <strong class="text-heading">{{ meta.total }}</strong> kursus</span>
            <span v-if="searchQuery"> untuk "<strong class="text-heading">{{ searchQuery }}</strong>"</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <CourseCard
              v-for="course in courses"
              :key="course.id"
              :course="course"
            />
          </div>

          <!-- Pagination -->
          <nav v-if="totalPages > 1" class="flex items-center justify-center gap-1.5 mt-12">
            <button
              type="button"
              :disabled="currentPage <= 1"
              class="p-2.5 rounded-xl border border-border text-muted hover:text-heading hover:border-primary-300 hover:bg-primary-50/50 transition-all disabled:opacity-30 disabled:cursor-not-allowed"
              @click="goToPage(currentPage - 1)"
            >
              <Icon name="lucide:chevron-left" class="w-4 h-4" />
            </button>

            <!-- First page + ellipsis -->
            <template v-if="firstPage > 1">
              <button
                type="button"
                class="w-10 h-10 rounded-xl text-sm font-medium border border-border text-body hover:border-primary-300 hover:text-primary-600 transition-all"
                @click="goToPage(1)"
              >
                1
              </button>
              <span v-if="firstPage > 2" class="px-1 text-muted">…</span>
            </template>

            <button
              v-for="page in pageNumbers"
              :key="page"
              type="button"
              class="w-10 h-10 rounded-xl text-sm font-medium transition-all"
              :class="
                page === currentPage
                  ? 'bg-primary-500 text-white shadow-sm'
                  : 'border border-border text-body hover:border-primary-300 hover:text-primary-600'
              "
              @click="goToPage(page)"
            >
              {{ page }}
            </button>

            <!-- Last page + ellipsis -->
            <template v-if="lastPage < totalPages">
              <span v-if="lastPage < totalPages - 1" class="px-1 text-muted">…</span>
              <button
                type="button"
                class="w-10 h-10 rounded-xl text-sm font-medium border border-border text-body hover:border-primary-300 hover:text-primary-600 transition-all"
                @click="goToPage(totalPages)"
              >
                {{ totalPages }}
              </button>
            </template>

            <button
              type="button"
              :disabled="currentPage >= totalPages"
              class="p-2.5 rounded-xl border border-border text-muted hover:text-heading hover:border-primary-300 hover:bg-primary-50/50 transition-all disabled:opacity-30 disabled:cursor-not-allowed"
              @click="goToPage(currentPage + 1)"
            >
              <Icon name="lucide:chevron-right" class="w-4 h-4" />
            </button>
          </nav>

          <!-- Results info -->
          <p v-if="meta" class="text-center text-xs text-muted mt-4">
            Halaman {{ meta.current_page }} dari {{ meta.last_page }} · {{ meta.total }} kursus
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
