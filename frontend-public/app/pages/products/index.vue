<script setup lang="ts">
/**
 * Product Listing Page
 * Route: /products
 *
 * FR-PR-P01: Display product list for guest and user
 * FR-PR-P03: Filter by category, price, and file type
 * FR-PR-P04: Search product by keyword
 *
 * UI pattern aligned with articles/index.vue and webinars/index.vue for consistency.
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Category, Product, ProductListParams } from '~~/types'

useSeo({
  title: 'Produk',
  description: 'Temukan produk edukasi PAUD berkualitas di PaudPedia — buku, worksheet, template, dan media belajar digital.',
})

// ─── State ───
const products = ref<Product[]>([])
const meta = ref<PaginationMeta | null>(null)
const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedPriceRange = ref('')
const selectedFileType = ref('')
const selectedSort = ref('')
const currentPage = ref(1)

// ─── Filter options ───
const priceRangeOptions = [
  { label: 'Semua Harga', value: '' },
  { label: 'Gratis', value: 'free' },
  { label: '< Rp50.000', value: '0-50000' },
  { label: 'Rp50.000 - Rp100.000', value: '50000-100000' },
  { label: 'Rp100.000 - Rp250.000', value: '100000-250000' },
  { label: '> Rp250.000', value: '250000+' },
]

const fileTypeOptions = [
  { label: 'Semua Tipe', value: '' },
  { label: 'PDF', value: 'pdf' },
  { label: 'DOC / DOCX', value: 'docx' },
  { label: 'PPT / PPTX', value: 'pptx' },
  { label: 'XLS / XLSX', value: 'xlsx' },
  { label: 'ZIP', value: 'zip' },
]

const sortOptions = [
  { label: 'Terbaru', value: '' },
  { label: 'Harga Terendah', value: 'price-asc' },
  { label: 'Harga Tertinggi', value: 'price-desc' },
  { label: 'Judul A-Z', value: 'title-asc' },
]

// ─── Build API params ───
function buildParams(): ProductListParams {
  const params: ProductListParams = {
    page: currentPage.value,
    per_page: 12,
  }
  if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
  if (selectedCategory.value) params.category = selectedCategory.value

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
    params.sort_by = field as ProductListParams['sort_by']
    params.sort_order = order as ProductListParams['sort_order']
  }

  return params
}

// ─── URL sync ───
const router = useRouter()

function syncQueryParams() {
  const query: Record<string, string> = {}
  if (searchQuery.value.trim()) query.search = searchQuery.value.trim()
  if (selectedCategory.value) query.category = selectedCategory.value
  if (selectedPriceRange.value) query.price = selectedPriceRange.value
  if (selectedFileType.value) query.file_type = selectedFileType.value
  if (selectedSort.value) query.sort = selectedSort.value
  if (currentPage.value > 1) query.page = String(currentPage.value)
  router.replace({ query })
}

// ─── Fetch categories from API ───
async function fetchCategories() {
  try {
    const { categoryService } = await import('~~/services')
    const res = await categoryService.getProductCategories()
    if (res.success) {
      categories.value = res.data
    }
  }
  catch {
    // Categories are an enhancement; empty list fallback is fine
  }
}

// ─── Fetch products ───
async function fetchProducts() {
  loading.value = true
  error.value = false
  try {
    const { productService } = await import('~~/services')
    const params = buildParams()

    // File type filter — send to backend
    if (selectedFileType.value) {
      (params as any).file_type = selectedFileType.value
    }

    const res = await productService.getList(params)
    if (res.success) {
      products.value = res.data
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
  fetchProducts()
}

function goToPage(page: number) {
  currentPage.value = page
  syncQueryParams()
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function clearAllFilters() {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedPriceRange.value = ''
  selectedFileType.value = ''
  selectedSort.value = ''
}

// ─── Watchers ───
let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch([selectedCategory, selectedPriceRange, selectedFileType, selectedSort], resetAndFetch)

onMounted(() => {
  const route = useRoute()
  if (route.query.search) searchQuery.value = route.query.search as string
  if (route.query.category) selectedCategory.value = route.query.category as string
  if (route.query.price) selectedPriceRange.value = route.query.price as string
  if (route.query.file_type) selectedFileType.value = route.query.file_type as string
  if (route.query.sort) selectedSort.value = route.query.sort as string
  if (route.query.page) currentPage.value = Number(route.query.page) || 1

  fetchCategories()
  fetchProducts()
})

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasProducts = computed(() => products.value.length > 0)

const hasActiveFilters = computed(() =>
  searchQuery.value.trim() !== ''
  || selectedCategory.value !== ''
  || selectedPriceRange.value !== ''
  || selectedFileType.value !== ''
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
      badge="Produk"
      badge-icon="lucide:package"
      title="Produk Digital"
      description="Temukan koleksi produk edukasi berkualitas — buku, worksheet, template, dan media belajar untuk mendukung pendidikan anak usia dini."
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
                placeholder="Cari produk..."
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

          <!-- Row 3: Price / File Type / Sort -->
          <div v-if="!loading" class="flex flex-wrap gap-2 items-center">
            <span class="text-[11px] text-muted font-medium mr-1">Filter:</span>

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

            <!-- File type -->
            <select
              v-model="selectedFileType"
              class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-all duration-200 bg-surface cursor-pointer appearance-none"
              :class="
                selectedFileType
                  ? 'bg-primary-100 text-primary-700 border-primary-300'
                  : 'border-border/60 text-muted hover:border-primary-200 hover:text-primary-600'
              "
            >
              <option v-for="opt in fileTypeOptions" :key="opt.value" :value="opt.value">
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
        <SkeletonCardGrid v-if="loading" :count="8" :columns="4" variant="media" />

        <!-- Error -->
        <div v-else-if="error" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-warning-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Gagal Memuat Produk</h3>
          <p class="text-sm text-body mb-6 max-w-sm mx-auto">Terjadi kesalahan saat memuat daftar produk. Silakan coba lagi.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
            @click="fetchProducts"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!hasProducts" class="text-center py-20">
          <div class="w-16 h-16 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-5">
            <Icon name="lucide:package-x" class="w-8 h-8 text-primary-400" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-2">Belum Ada Produk</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilters">
              Tidak ditemukan produk yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline font-medium ml-1" @click="clearAllFilters">
                Reset filter
              </button>
            </template>
            <template v-else>
              Produk akan segera hadir. Nantikan katalog terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Product Grid -->
        <template v-else>
          <!-- Results summary -->
          <div v-if="meta && hasActiveFilters" class="mb-6 flex items-center gap-2 text-sm text-muted">
            <Icon name="lucide:filter" class="w-4 h-4" />
            <span>Menampilkan <strong class="text-heading">{{ meta.total }}</strong> produk</span>
            <span v-if="searchQuery"> untuk "<strong class="text-heading">{{ searchQuery }}</strong>"</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <ProductCard
              v-for="product in products"
              :key="product.id"
              :product="product"
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
            Halaman {{ meta.current_page }} dari {{ meta.last_page }} · {{ meta.total }} produk
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
