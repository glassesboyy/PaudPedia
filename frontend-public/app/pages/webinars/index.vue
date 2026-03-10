<script setup lang="ts">
/**
 * Webinar Listing Page
 * Route: /webinars
 *
 * FR-WB-P01: Display webinar list for guest and user
 * FR-WB-P04: Search webinar by keyword
 * FR-WB-P05: Filter by status, price, date
 *
 * UI pattern aligned with articles/index.vue for consistency.
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Webinar, WebinarListParams } from '~~/types'

useSeo({
  title: 'Webinar',
  description: 'Ikuti webinar PAUD terbaik dari para ahli di PaudPedia.',
})

// ─── State ───
const webinars = ref<Webinar[]>([])
const meta = ref<PaginationMeta | null>(null)
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedStatus = ref('')
const selectedPriceRange = ref('')
const selectedDateRange = ref('')
const selectedSort = ref('')
const currentPage = ref(1)

// ─── Filter options ───
const statusOptions = [
  { label: 'Semua', value: '' },
  { label: 'Akan Datang', value: 'upcoming' },
  { label: 'Sudah Selesai', value: 'past' },
]

const priceRangeOptions = [
  { label: 'Semua Harga', value: '' },
  { label: 'Gratis', value: 'free' },
  { label: '< Rp100.000', value: '0-100000' },
  { label: 'Rp100.000 - Rp250.000', value: '100000-250000' },
  { label: '> Rp250.000', value: '250000+' },
]

const sortOptions = [
  { label: 'Terbaru', value: '' },
  { label: 'Jadwal Terdekat', value: 'scheduled_at-asc' },
  { label: 'Harga Terendah', value: 'price-asc' },
  { label: 'Harga Tertinggi', value: 'price-desc' },
  { label: 'Judul A-Z', value: 'title-asc' },
]

// ─── Build API params ───
function buildParams(): WebinarListParams {
  const params: WebinarListParams = {
    page: currentPage.value,
    per_page: 12,
  }
  if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
  if (selectedStatus.value === 'upcoming') params.upcoming = true
  if (selectedStatus.value === 'past') params.past = true
  if (selectedPriceRange.value === 'free') {
    params.free = true
  }
  else if (selectedPriceRange.value) {
    const [min, max] = selectedPriceRange.value.split('-')
    if (min) params.min_price = Number(min)
    if (max && max !== '+') params.max_price = Number(max)
  }
  if (selectedDateRange.value) params.start_date = selectedDateRange.value
  if (selectedSort.value) {
    const [field, order] = selectedSort.value.split('-')
    params.sort_by = field as WebinarListParams['sort_by']
    params.sort_order = order as WebinarListParams['sort_order']
  }
  return params
}

// ─── URL sync ───
const router = useRouter()

function syncQueryParams() {
  const query: Record<string, string> = {}
  if (searchQuery.value.trim()) query.search = searchQuery.value.trim()
  if (selectedStatus.value) query.status = selectedStatus.value
  if (selectedPriceRange.value) query.price = selectedPriceRange.value
  if (selectedDateRange.value) query.date = selectedDateRange.value
  if (selectedSort.value) query.sort = selectedSort.value
  if (currentPage.value > 1) query.page = String(currentPage.value)
  router.replace({ query })
}

// ─── Fetch ───
async function fetchWebinars() {
  loading.value = true
  error.value = false
  try {
    const { webinarService } = await import('~~/services')
    const res = await webinarService.getList(buildParams())
    if (res.success) {
      webinars.value = res.data
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
  fetchWebinars()
}

function goToPage(page: number) {
  currentPage.value = page
  syncQueryParams()
  fetchWebinars()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function clearAllFilters() {
  searchQuery.value = ''
  selectedStatus.value = ''
  selectedPriceRange.value = ''
  selectedDateRange.value = ''
  selectedSort.value = ''
}

// ─── Watchers ───
let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch([selectedStatus, selectedPriceRange, selectedDateRange, selectedSort], resetAndFetch)

onMounted(() => {
  const route = useRoute()
  if (route.query.search) searchQuery.value = route.query.search as string
  if (route.query.status) selectedStatus.value = route.query.status as string
  if (route.query.price) selectedPriceRange.value = route.query.price as string
  if (route.query.date) selectedDateRange.value = route.query.date as string
  if (route.query.sort) selectedSort.value = route.query.sort as string
  if (route.query.page) currentPage.value = Number(route.query.page) || 1

  fetchWebinars()
})

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasWebinars = computed(() => webinars.value.length > 0)

const hasActiveFilters = computed(() =>
  searchQuery.value.trim() !== '' || selectedStatus.value !== '' || selectedPriceRange.value !== '' || selectedDateRange.value !== '' || selectedSort.value !== '',
)

const hasAdvancedFilters = computed(() =>
  selectedPriceRange.value !== '' || selectedDateRange.value !== '' || selectedSort.value !== '',
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
      badge="Webinar"
      badge-icon="lucide:video"
      title="Webinar"
      description="Ikuti webinar interaktif dari para ahli pendidikan anak usia dini. Tingkatkan kompetensi Anda dari mana saja."
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
                placeholder="Cari webinar..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-secondary-200 focus:border-secondary-400 transition-all"
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

          <!-- Row 2: Status pills -->
          <div class="flex flex-wrap gap-2">
            <button
              v-for="opt in statusOptions"
              :key="opt.value"
              type="button"
              class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-all duration-200"
              :class="
                selectedStatus === opt.value
                  ? 'bg-secondary-500 text-white border-secondary-500 shadow-sm'
                  : 'bg-surface border-border text-body hover:border-secondary-300 hover:text-secondary-600'
              "
              @click="selectedStatus = opt.value"
            >
              {{ opt.label }}
            </button>
          </div>

          <!-- Row 3: Price / Date / Sort (always visible) -->
          <div v-if="!loading" class="flex flex-wrap gap-2 items-center">
            <span class="text-[11px] text-muted font-medium mr-1">Filter:</span>

            <select
              v-model="selectedPriceRange"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200 bg-surface cursor-pointer appearance-none"
              :class="
                selectedPriceRange
                  ? 'bg-secondary-100 text-secondary-700 border-secondary-300'
                  : 'border-border/60 text-muted hover:border-secondary-200 hover:text-secondary-600'
              "
            >
              <option v-for="opt in priceRangeOptions" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>

            <div class="relative">
              <input
                v-model="selectedDateRange"
                type="date"
                class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200 bg-surface cursor-pointer"
                :class="
                  selectedDateRange
                    ? 'bg-secondary-100 text-secondary-700 border-secondary-300'
                    : 'border-border/60 text-muted hover:border-secondary-200 hover:text-secondary-600'
                "
                title="Dari tanggal"
              />
            </div>

            <select
              v-model="selectedSort"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200 bg-surface cursor-pointer appearance-none"
              :class="
                selectedSort
                  ? 'bg-secondary-100 text-secondary-700 border-secondary-300'
                  : 'border-border/60 text-muted hover:border-secondary-200 hover:text-secondary-600'
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
          <h3 class="text-lg font-semibold text-heading mb-2">Gagal Memuat Webinar</h3>
          <p class="text-sm text-body mb-6 max-w-sm mx-auto">Terjadi kesalahan saat memuat daftar webinar. Silakan coba lagi.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-secondary-500 text-white text-sm font-medium hover:bg-secondary-600 transition-colors shadow-sm"
            @click="fetchWebinars"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!hasWebinars" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-secondary-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:video-off" class="w-8 h-8 text-secondary-400" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Belum Ada Webinar</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilters">
              Tidak ditemukan webinar yang sesuai dengan filter Anda.
              <button type="button" class="text-secondary-600 hover:underline font-medium ml-1" @click="clearAllFilters">
                Reset filter
              </button>
            </template>
            <template v-else>
              Webinar akan segera hadir. Nantikan jadwal terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Webinar Grid -->
        <template v-else>
          <!-- Results summary -->
          <div v-if="meta && hasActiveFilters" class="mb-6 flex items-center gap-2 text-sm text-muted">
            <Icon name="lucide:filter" class="w-4 h-4" />
            <span>Menampilkan <strong class="text-heading">{{ meta.total }}</strong> webinar</span>
            <span v-if="searchQuery"> untuk "<strong class="text-heading">{{ searchQuery }}</strong>"</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <WebinarCard
              v-for="webinar in webinars"
              :key="webinar.id"
              :webinar="webinar"
            />
          </div>

          <!-- Pagination -->
          <nav v-if="totalPages > 1" class="flex items-center justify-center gap-1.5 mt-12">
            <button
              type="button"
              :disabled="currentPage <= 1"
              class="p-2.5 rounded-xl border border-border text-muted hover:text-heading hover:border-secondary-300 hover:bg-secondary-50/50 transition-all disabled:opacity-30 disabled:cursor-not-allowed"
              @click="goToPage(currentPage - 1)"
            >
              <Icon name="lucide:chevron-left" class="w-4 h-4" />
            </button>

            <!-- First page + ellipsis -->
            <template v-if="firstPage > 1">
              <button
                type="button"
                class="w-10 h-10 rounded-xl text-sm font-medium border border-border text-body hover:border-secondary-300 hover:text-secondary-600 transition-all"
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
                  ? 'bg-secondary-500 text-white shadow-sm'
                  : 'border border-border text-body hover:border-secondary-300 hover:text-secondary-600'
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
                class="w-10 h-10 rounded-xl text-sm font-medium border border-border text-body hover:border-secondary-300 hover:text-secondary-600 transition-all"
                @click="goToPage(totalPages)"
              >
                {{ totalPages }}
              </button>
            </template>

            <button
              type="button"
              :disabled="currentPage >= totalPages"
              class="p-2.5 rounded-xl border border-border text-muted hover:text-heading hover:border-secondary-300 hover:bg-secondary-50/50 transition-all disabled:opacity-30 disabled:cursor-not-allowed"
              @click="goToPage(currentPage + 1)"
            >
              <Icon name="lucide:chevron-right" class="w-4 h-4" />
            </button>
          </nav>

          <!-- Results info -->
          <p v-if="meta" class="text-center text-xs text-muted mt-4">
            Halaman {{ meta.current_page }} dari {{ meta.last_page }} · {{ meta.total }} webinar
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
