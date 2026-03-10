<script setup lang="ts">
/**
 * Webinar Listing Page
 * Route: /webinars
 *
 * Full listing with search, status filter, and pagination.
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
const currentPage = ref(1)

// ─── Fetch ───
async function fetchWebinars() {
  loading.value = true
  error.value = false
  try {
    const { webinarService } = await import('~~/services')
    const params: WebinarListParams = {
      page: currentPage.value,
      per_page: 12,
    }
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (selectedStatus.value) params.status = selectedStatus.value as WebinarListParams['status']

    const res = await webinarService.getList(params)
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

// ─── Watchers ───
function resetAndFetch() {
  currentPage.value = 1
  fetchWebinars()
}

function goToPage(page: number) {
  currentPage.value = page
  fetchWebinars()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch(selectedStatus, resetAndFetch)

onMounted(fetchWebinars)

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasWebinars = computed(() => webinars.value.length > 0)
const hasActiveFilter = computed(() => !!searchQuery.value || !!selectedStatus.value)

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
const statuses = [
  { label: 'Semua', value: '' },
  { label: 'Akan Datang', value: 'upcoming' },
  { label: 'Sedang Live', value: 'live' },
  { label: 'Selesai', value: 'past' },
]

function clearFilters() {
  searchQuery.value = ''
  selectedStatus.value = ''
}
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-secondary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Webinar</h1>
        <p class="mt-4 text-base text-body max-w-2xl mx-auto leading-relaxed">
          Ikuti webinar interaktif dari para ahli pendidikan anak usia dini.
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
              placeholder="Cari webinar..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-surface text-sm text-heading placeholder:text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors"
            />
          </div>

          <!-- Status Filter -->
          <div class="flex flex-wrap gap-2">
            <button
              v-for="status in statuses"
              :key="status.value"
              type="button"
              class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-colors"
              :class="
                selectedStatus === status.value
                  ? 'bg-secondary-500 text-white border-secondary-500'
                  : 'bg-surface border-border text-body hover:border-secondary-300 hover:text-secondary-600'
              "
              @click="selectedStatus = status.value"
            >
              {{ status.label }}
            </button>
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
        <div v-else-if="error" class="text-center py-14">
          <Icon name="lucide:alert-triangle" class="w-12 h-12 text-warning-500 mx-auto mb-4" />
          <p class="text-sm text-body mb-4">Gagal memuat daftar webinar.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors"
            @click="fetchWebinars"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty -->
        <div v-else-if="!hasWebinars" class="text-center py-14">
          <Icon name="lucide:video" class="w-12 h-12 text-muted mx-auto mb-4" />
          <h3 class="text-base font-semibold text-heading mb-2">Belum Ada Webinar</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilter">
              Tidak ditemukan webinar yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline ml-1" @click="clearFilters">
                Reset filter
              </button>
            </template>
            <template v-else>
              Webinar akan segera hadir. Nantikan jadwal terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Grid -->
        <template v-else>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <WebinarCard
              v-for="webinar in webinars"
              :key="webinar.id"
              :webinar="webinar"
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
            Menampilkan {{ webinars.length }} dari {{ meta.total }} webinar
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
