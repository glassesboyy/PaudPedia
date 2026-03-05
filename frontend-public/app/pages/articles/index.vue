<script setup lang="ts">
/**
 * Article Listing Page
 * Route: /articles
 *
 * FR-PP-04: Halaman Artikel — Full listing with search, category filter, pagination.
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Article, ArticleListParams } from '~~/types'

useSeo({
  title: 'Artikel',
  description: 'Baca artikel edukasi seputar pendidikan anak usia dini di PaudPedia.',
})

// ─── State ───
const articles = ref<Article[]>([])
const meta = ref<PaginationMeta | null>(null)
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedCategory = ref('')
const currentPage = ref(1)

// ─── Fetch ───
async function fetchArticles() {
  loading.value = true
  error.value = false
  try {
    const { articleService } = await import('~~/services')
    const params: ArticleListParams = {
      page: currentPage.value,
      per_page: 12,
    }
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (selectedCategory.value) params.category = selectedCategory.value

    const res = await articleService.getList(params)
    if (res.success) {
      articles.value = res.data
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
  fetchArticles()
}

function goToPage(page: number) {
  currentPage.value = page
  fetchArticles()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch(selectedCategory, resetAndFetch)

onMounted(fetchArticles)

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasArticles = computed(() => articles.value.length > 0)

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

// ─── Categories (static for now) ───
const categories = [
  { label: 'Semua', value: '' },
  { label: 'Kurikulum', value: 'kurikulum' },
  { label: 'Metode Belajar', value: 'metode-belajar' },
  { label: 'Perkembangan Anak', value: 'perkembangan-anak' },
  { label: 'Tips & Trik', value: 'tips-trik' },
  { label: 'Parenting', value: 'parenting' },
]
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-primary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Artikel</h1>
        <p class="mt-4 text-base text-body max-w-2xl mx-auto leading-relaxed">
          Baca artikel edukasi seputar pendidikan anak usia dini dari para ahli dan praktisi.
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
              placeholder="Cari artikel..."
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
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="bg-surface">
      <div class="container py-10 sm:py-14">
        <!-- Loading -->
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="i in 6"
            :key="i"
            class="rounded-2xl border border-border bg-surface overflow-hidden animate-pulse"
          >
            <div class="aspect-video bg-muted/10" />
            <div class="p-4 space-y-3">
              <div class="h-3 w-16 bg-muted/10 rounded" />
              <div class="h-4 w-3/4 bg-muted/10 rounded" />
              <div class="h-3 w-full bg-muted/10 rounded" />
              <div class="h-3 w-1/2 bg-muted/10 rounded" />
            </div>
          </div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-center py-14">
          <Icon name="lucide:alert-triangle" class="w-12 h-12 text-warning-500 mx-auto mb-4" />
          <p class="text-sm text-body mb-4">Gagal memuat artikel.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors"
            @click="fetchArticles"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!hasArticles" class="text-center py-14">
          <Icon name="lucide:newspaper" class="w-12 h-12 text-muted mx-auto mb-4" />
          <h3 class="text-base font-semibold text-heading mb-2">Belum Ada Artikel</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="searchQuery || selectedCategory">
              Tidak ditemukan artikel yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline ml-1" @click="searchQuery = ''; selectedCategory = ''">
                Reset filter
              </button>
            </template>
            <template v-else>
              Artikel akan segera hadir. Nantikan konten terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Article Grid -->
        <template v-else>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <ArticleCard
              v-for="article in articles"
              :key="article.id"
              :article="article"
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

          <!-- Results info -->
          <p v-if="meta" class="text-center text-xs text-muted mt-4">
            Menampilkan {{ articles.length }} dari {{ meta.total }} artikel
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
