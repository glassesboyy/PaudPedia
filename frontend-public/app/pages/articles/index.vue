<script setup lang="ts">
/**
 * Article Listing Page
 * Route: /articles
 *
 * FR-AR-P01: Display article list for guest and user
 * FR-AR-P03: Filter articles by category and tags
 * FR-AR-P04: Search articles by keyword
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Article, ArticleListParams, Category } from '~~/types'

useSeo({
  title: 'Artikel',
  description: 'Baca artikel edukasi seputar pendidikan anak usia dini di PaudPedia.',
})

// ─── State ───
const articles = ref<Article[]>([])
const meta = ref<PaginationMeta | null>(null)
const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedTag = ref('')
const currentPage = ref(1)

// ─── Fetch categories from API ───
async function fetchCategories() {
  try {
    const { categoryService } = await import('~~/services')
    const res = await categoryService.getArticleCategories()
    if (res.success) {
      categories.value = res.data
    }
  }
  catch {
    // Categories are an enhancement, keep static fallback
  }
}

// ─── Fetch articles ───
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
    if (selectedTag.value) params.tag = selectedTag.value

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
const router = useRouter()

function syncQueryParams() {
  const query: Record<string, string> = {}
  if (searchQuery.value.trim()) query.search = searchQuery.value.trim()
  if (selectedCategory.value) query.category = selectedCategory.value
  if (selectedTag.value) query.tag = selectedTag.value
  if (currentPage.value > 1) query.page = String(currentPage.value)
  router.replace({ query })
}

function resetAndFetch() {
  currentPage.value = 1
  syncQueryParams()
  fetchArticles()
}

function goToPage(page: number) {
  currentPage.value = page
  syncQueryParams()
  fetchArticles()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch(selectedCategory, resetAndFetch)
watch(selectedTag, resetAndFetch)

onMounted(() => {
  // Read initial query params
  const route = useRoute()
  if (route.query.category) selectedCategory.value = route.query.category as string
  if (route.query.tag) selectedTag.value = route.query.tag as string
  if (route.query.search) searchQuery.value = route.query.search as string
  if (route.query.page) currentPage.value = Number(route.query.page) || 1

  fetchCategories()
  fetchArticles()
})

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

const firstPage = computed(() => pageNumbers.value[0] ?? 1)
const lastPage = computed(() => pageNumbers.value[pageNumbers.value.length - 1] ?? 1)

// ─── Collect unique tags from current articles ───
function normalizeTags(tags: unknown): string[] {
  if (Array.isArray(tags)) return tags
  if (typeof tags === 'string' && tags.trim()) return tags.split(',').map(t => t.trim())
  return []
}

const availableTags = computed(() => {
  const tagSet = new Set<string>()
  articles.value.forEach((a) => {
    normalizeTags(a.tags).forEach(t => tagSet.add(t))
  })
  return Array.from(tagSet).sort()
})

const hasActiveFilters = computed(() => {
  return searchQuery.value.trim() !== '' || selectedCategory.value !== '' || selectedTag.value !== ''
})

function clearAllFilters() {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedTag.value = ''
}

function selectTag(tag: string) {
  selectedTag.value = selectedTag.value === tag ? '' : tag
}
</script>

<template>
  <div>
    <!-- Hero -->
    <PageHero
      badge="Artikel"
      badge-icon="lucide:newspaper"
      title="Artikel & Edukasi"
      description="Baca artikel edukasi seputar pendidikan anak usia dini dari para ahli dan praktisi."
      variant="gradient"
    />

    <!-- Filters -->
    <section class="bg-surface border-b border-border/50 sticky top-0 z-40">
      <div class="container py-4">
        <div class="flex flex-col gap-3">
          <!-- Row 1: Search + active filter clear -->
          <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
              <Icon name="lucide:search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari artikel..."
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
              @click="selectedCategory = selectedCategory === cat.slug ? '' : cat.slug"
            >
              {{ cat.name }}
            </button>
          </div>

          <!-- Row 3: Tag pills (only show if tags are available) -->
          <div v-if="availableTags.length > 0 && !loading" class="flex flex-wrap gap-1.5">
            <span class="text-[11px] text-muted font-medium mr-1 self-center">Tags:</span>
            <button
              v-for="tag in availableTags.slice(0, 12)"
              :key="tag"
              type="button"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200"
              :class="
                selectedTag === tag
                  ? 'bg-primary-100 text-primary-700 border-primary-300'
                  : 'bg-surface border-border/60 text-muted hover:border-primary-200 hover:text-primary-600'
              "
              @click="selectTag(tag)"
            >
              #{{ tag }}
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
            <div class="aspect-[16/10] bg-muted/10" />
            <div class="p-5 space-y-3">
              <div class="flex gap-1.5">
                <div class="h-4 w-14 bg-muted/10 rounded-full" />
                <div class="h-4 w-12 bg-muted/10 rounded-full" />
              </div>
              <div class="h-5 w-3/4 bg-muted/10 rounded" />
              <div class="h-4 w-full bg-muted/10 rounded" />
              <div class="h-4 w-2/3 bg-muted/10 rounded" />
              <div class="pt-3 border-t border-border/40 flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full bg-muted/10" />
                  <div class="h-3 w-20 bg-muted/10 rounded" />
                </div>
                <div class="h-3 w-12 bg-muted/10 rounded" />
              </div>
            </div>
          </div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-warning-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Gagal Memuat Artikel</h3>
          <p class="text-sm text-body mb-6 max-w-sm mx-auto">Terjadi kesalahan saat memuat daftar artikel. Silakan coba lagi.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
            @click="fetchArticles"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!hasArticles" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:newspaper" class="w-8 h-8 text-primary-400" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Belum Ada Artikel</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilters">
              Tidak ditemukan artikel yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline font-medium ml-1" @click="clearAllFilters">
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
          <!-- Results summary -->
          <div v-if="meta && hasActiveFilters" class="mb-6 flex items-center gap-2 text-sm text-muted">
            <Icon name="lucide:filter" class="w-4 h-4" />
            <span>Menampilkan <strong class="text-heading">{{ meta.total }}</strong> artikel</span>
            <span v-if="searchQuery"> untuk "<strong class="text-heading">{{ searchQuery }}</strong>"</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <ArticleCard
              v-for="article in articles"
              :key="article.id"
              :article="article"
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
            Halaman {{ meta.current_page }} dari {{ meta.last_page }} · {{ meta.total }} artikel
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
