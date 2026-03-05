<script setup lang="ts">
/**
 * Product Listing Page
 * Route: /products
 *
 * Full listing with search, category/type filters, and pagination.
 */
import type { PaginationMeta } from '~~/services/api/types'
import type { Product, ProductListParams } from '~~/types'

useSeo({
  title: 'Produk',
  description: 'Temukan produk pendidikan PAUD terbaik di PaudPedia.',
})

// ─── State ───
const products = ref<Product[]>([])
const meta = ref<PaginationMeta | null>(null)
const loading = ref(true)
const error = ref(false)

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedType = ref<'' | 'digital' | 'physical'>('')
const currentPage = ref(1)

// ─── Fetch ───
async function fetchProducts() {
  loading.value = true
  error.value = false
  try {
    const { productService } = await import('~~/services')
    const params: ProductListParams = {
      page: currentPage.value,
      per_page: 12,
    }
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim()
    if (selectedCategory.value) params.category = selectedCategory.value
    if (selectedType.value) params.type = selectedType.value

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

// ─── Watchers ───
function resetAndFetch() {
  currentPage.value = 1
  fetchProducts()
}

function goToPage(page: number) {
  currentPage.value = page
  fetchProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(resetAndFetch, 400)
})

watch(selectedCategory, resetAndFetch)
watch(selectedType, resetAndFetch)

onMounted(fetchProducts)

// ─── Computed ───
const totalPages = computed(() => meta.value?.last_page ?? 1)
const hasProducts = computed(() => products.value.length > 0)
const hasActiveFilter = computed(() => !!searchQuery.value || !!selectedCategory.value || !!selectedType.value)

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
  { label: 'Buku', value: 'buku' },
  { label: 'Alat Peraga', value: 'alat-peraga' },
  { label: 'Media Belajar', value: 'media-belajar' },
  { label: 'Worksheet', value: 'worksheet' },
  { label: 'Template', value: 'template' },
]

const types = [
  { label: 'Semua Tipe', value: '' },
  { label: 'Digital', value: 'digital' },
  { label: 'Fisik', value: 'physical' },
]

function clearFilters() {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedType.value = ''
}
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-success-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Produk</h1>
        <p class="mt-4 text-base text-body max-w-2xl mx-auto leading-relaxed">
          Temukan produk edukasi berkualitas untuk mendukung pendidikan anak usia dini.
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
              placeholder="Cari produk..."
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

          <!-- Type Filter -->
          <select
            v-model="selectedType"
            class="px-3 py-2 rounded-xl border border-border bg-surface text-sm text-heading focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-400 transition-colors"
          >
            <option v-for="t in types" :key="t.value" :value="t.value">
              {{ t.label }}
            </option>
          </select>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="bg-surface">
      <div class="container py-10 sm:py-14">
        <!-- Loading -->
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="i in 8"
            :key="i"
            class="rounded-2xl border border-border bg-surface overflow-hidden animate-pulse"
          >
            <div class="aspect-video bg-muted/10" />
            <div class="p-4 space-y-3">
              <div class="h-3 w-16 bg-muted/10 rounded" />
              <div class="h-4 w-3/4 bg-muted/10 rounded" />
              <div class="h-3 w-full bg-muted/10 rounded" />
            </div>
          </div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-center py-14">
          <Icon name="lucide:alert-triangle" class="w-12 h-12 text-warning-500 mx-auto mb-4" />
          <p class="text-sm text-body mb-4">Gagal memuat daftar produk.</p>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors"
            @click="fetchProducts"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>

        <!-- Empty -->
        <div v-else-if="!hasProducts" class="text-center py-14">
          <Icon name="lucide:package" class="w-12 h-12 text-muted mx-auto mb-4" />
          <h3 class="text-base font-semibold text-heading mb-2">Belum Ada Produk</h3>
          <p class="text-sm text-body max-w-sm mx-auto">
            <template v-if="hasActiveFilter">
              Tidak ditemukan produk yang sesuai dengan filter Anda.
              <button type="button" class="text-primary-600 hover:underline ml-1" @click="clearFilters">
                Reset filter
              </button>
            </template>
            <template v-else>
              Produk akan segera hadir. Nantikan katalog terbaru dari kami!
            </template>
          </p>
        </div>

        <!-- Grid -->
        <template v-else>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <ProductCard
              v-for="product in products"
              :key="product.id"
              :product="product"
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
            Menampilkan {{ products.length }} dari {{ meta.total }} produk
          </p>
        </template>
      </div>
    </section>
  </div>
</template>
