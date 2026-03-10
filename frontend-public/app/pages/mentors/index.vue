<script setup lang="ts">
/**
 * Mentor Listing Page
 * Route: /mentors
 *
 * FR-MN-P01: Display mentor list for guest and user
 * Supports search by name/title/bio and filtering by expertise.
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Mentor, MentorListParams } from '~~/types'

useSeo({
  title: 'Mentor',
  description: 'Temukan mentor PAUD terbaik untuk belajar dan berkembang bersama.',
})

// ─── State ───
const mentors = ref<Mentor[]>([])
const meta = ref<PaginationMeta | null>(null)
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedExpertise = ref('')
const currentPage = ref(1)

// ─── Fetch mentors ───
async function fetchMentors() {
  loading.value = true
  error.value = false
  try {
    const { mentorService } = await import('~~/services')
    const params: MentorListParams = {
      page: currentPage.value,
      per_page: 12,
    }
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (selectedExpertise.value) params.expertise = selectedExpertise.value

    const res = await mentorService.getList(params)
    if (res.success) {
      mentors.value = res.data
      meta.value = res.meta
    } else {
      error.value = true
    }
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
}

// ─── Watchers & URL sync ───
const router = useRouter()

function syncQueryParams() {
  const query: Record<string, string> = {}
  if (searchQuery.value.trim()) query.search = searchQuery.value.trim()
  if (selectedExpertise.value) query.expertise = selectedExpertise.value
  if (currentPage.value > 1) query.page = String(currentPage.value)
  router.replace({ query })
}

function resetAndFetch() {
  currentPage.value = 1
  syncQueryParams()
  fetchMentors()
}

function goToPage(page: number) {
  currentPage.value = page
  syncQueryParams()
  fetchMentors()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch(selectedExpertise, resetAndFetch)

onMounted(() => {
  const route = useRoute()
  if (route.query.search) searchQuery.value = route.query.search as string
  if (route.query.expertise) selectedExpertise.value = route.query.expertise as string
  if (route.query.page) currentPage.value = Number(route.query.page) || 1
  fetchMentors()
})

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasMentors = computed(() => mentors.value.length > 0)

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

/** Collect unique expertise keywords from current results */
const availableExpertise = computed(() => {
  const set = new Set<string>()
  mentors.value.forEach((m) => {
    if (m.expertise) {
      m.expertise.split(',').map(s => s.trim()).filter(Boolean).forEach(e => set.add(e))
    }
  })
  return Array.from(set).sort()
})

const hasActiveFilters = computed(() => {
  return searchQuery.value.trim() !== '' || selectedExpertise.value !== ''
})

function clearAllFilters() {
  searchQuery.value = ''
  selectedExpertise.value = ''
}
</script>

<template>
  <div>
    <!-- Hero -->
    <PageHero
      badge="Mentor"
      badge-icon="lucide:users"
      title="Mentor"
      highlight="PAUD Terbaik"
      description="Belajar langsung dari para ahli dan praktisi pendidikan anak usia dini yang berpengalaman."
      variant="gradient"
    />

    <!-- Filters -->
    <section class="bg-surface border-b border-border/50 sticky top-0 z-40">
      <div class="container py-4">
        <div class="flex flex-col gap-3">
          <!-- Search + clear -->
          <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            <div class="relative flex-1 max-w-md">
              <Icon name="lucide:search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari mentor..."
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

          <!-- Expertise pills -->
          <div v-if="availableExpertise.length > 0 && !loading" class="flex flex-wrap gap-1.5">
            <span class="text-[11px] text-muted font-medium mr-1 self-center">Keahlian:</span>
            <button
              v-for="exp in availableExpertise"
              :key="exp"
              type="button"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200"
              :class="
                selectedExpertise === exp
                  ? 'bg-primary-100 text-primary-700 border-primary-300'
                  : 'bg-surface border-border/60 text-muted hover:border-primary-200 hover:text-primary-600'
              "
              @click="selectedExpertise = selectedExpertise === exp ? '' : exp"
            >
              {{ exp }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="bg-surface">
      <div class="container py-10 sm:py-14">
        <!-- Loading skeletons -->
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="i in 8"
            :key="i"
            class="rounded-2xl border border-border bg-surface p-6 animate-pulse"
          >
            <div class="w-20 h-20 rounded-2xl bg-muted/10 mx-auto mb-4" />
            <div class="h-4 w-2/3 bg-muted/10 rounded mx-auto mb-2" />
            <div class="h-3 w-1/2 bg-muted/10 rounded mx-auto mb-3" />
            <div class="flex justify-center gap-1.5 mb-3">
              <div class="h-5 w-16 bg-muted/10 rounded-full" />
              <div class="h-5 w-14 bg-muted/10 rounded-full" />
            </div>
            <div class="h-3 w-full bg-muted/10 rounded mb-1" />
            <div class="h-3 w-3/4 bg-muted/10 rounded mx-auto mb-3" />
            <div class="flex justify-center gap-4">
              <div class="h-3 w-16 bg-muted/10 rounded" />
              <div class="h-3 w-16 bg-muted/10 rounded" />
            </div>
          </div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-warning-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Gagal Memuat Mentor</h3>
          <p class="text-sm text-body mb-6 max-w-sm mx-auto">Terjadi kesalahan saat memuat daftar mentor. Silakan coba lagi.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
            @click="fetchMentors"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty -->
        <div v-else-if="!hasMentors" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:users" class="w-8 h-8 text-primary-400" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Belum Ada Mentor</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilters">
              Tidak ditemukan mentor yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline font-medium ml-1" @click="clearAllFilters">
                Reset filter
              </button>
            </template>
            <template v-else>
              Mentor akan segera hadir. Nantikan konten terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Mentor grid -->
        <template v-else>
          <div v-if="meta && hasActiveFilters" class="mb-6 flex items-center gap-2 text-sm text-muted">
            <Icon name="lucide:filter" class="w-4 h-4" />
            <span>Menampilkan <strong class="text-heading">{{ meta.total }}</strong> mentor</span>
            <span v-if="searchQuery"> untuk "<strong class="text-heading">{{ searchQuery }}</strong>"</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <MentorCard
              v-for="mentor in mentors"
              :key="mentor.id"
              :mentor="mentor"
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

          <p v-if="meta" class="text-center text-xs text-muted mt-4">
            Halaman {{ meta.current_page }} dari {{ meta.last_page }} · {{ meta.total }} mentor
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
