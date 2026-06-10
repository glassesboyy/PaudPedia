<script setup lang="ts">
/**
 * My Products Page (FR-UA-09)
 * Route: /account/products
 *
 * Shows purchased products with download buttons.
 */
import { dashboardService } from '~~/services'
import type { UserProduct } from '~~/types'

definePageMeta({
  layout: 'dashboard',
  middleware: ['auth'],
})

useSeo({ title: 'Produk Saya' })

const products = ref<UserProduct[]>([])
const isLoading = ref(true)
const error = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)

async function fetchProducts(page = 1) {
  isLoading.value = true
  error.value = ''
  try {
    const res = await dashboardService.getProducts({ page, per_page: 12 })
    products.value = res.data
    currentPage.value = res.meta.current_page
    totalPages.value = res.meta.last_page
    totalItems.value = res.meta.total
  } catch {
    error.value = 'Gagal memuat data produk.'
  } finally {
    isLoading.value = false
  }
}

const isDownloading = ref<number | null>(null)

async function handleDownload(item: UserProduct) {
  if (!item.product_id || isDownloading.value === item.product_id) return
  isDownloading.value = item.product_id
  try {
    const blob = await dashboardService.downloadProduct(item.product_id)
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = item.slug ? `${item.slug}.${item.file_info?.type || 'bin'}` : `product-${item.product_id}`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  } catch {
    error.value = 'Gagal mengunduh file produk.'
  } finally {
    isDownloading.value = null
  }
}

function fileTypeColor(type: string | undefined): string {
  if (!type) return 'bg-surface-muted text-muted'
  const map: Record<string, string> = {
    pdf: 'bg-danger-50 text-danger-600',
    doc: 'bg-primary-50 text-primary-600',
    docx: 'bg-primary-50 text-primary-600',
    xls: 'bg-success-50 text-success-600',
    xlsx: 'bg-success-50 text-success-600',
    ppt: 'bg-secondary-50 text-secondary-600',
    pptx: 'bg-secondary-50 text-secondary-600',
    zip: 'bg-warning-50 text-warning-600',
  }
  return map[type.toLowerCase()] ?? 'bg-surface-muted text-muted'
}

onMounted(() => fetchProducts())
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold text-heading">Produk Saya</h1>
        <span
          v-if="!isLoading && totalItems > 0"
          class="text-xs font-medium text-muted bg-surface-muted px-2.5 py-1 rounded-full"
        >
          {{ totalItems }} produk
        </span>
      </div>
      <p class="text-sm text-muted">Akses dan unduh semua produk digital yang telah Anda beli.</p>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-for="n in 6" :key="n" class="rounded-2xl border border-border bg-surface overflow-hidden">
        <USkeleton variant="rectangular" class="aspect-[4/3] w-full" />
        <div class="p-4 space-y-3">
          <USkeleton variant="text" class="w-20 h-5 rounded-full" />
          <USkeleton variant="text" class="w-3/4 h-4" />
          <USkeleton variant="text" class="w-1/2 h-3" />
          <USkeleton variant="rectangular" class="w-full h-9 rounded-lg" />
        </div>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="rounded-2xl border border-danger-200 bg-danger-50/50 p-8 text-center">
      <div class="w-14 h-14 rounded-2xl bg-danger-100 flex items-center justify-center mx-auto mb-4">
        <Icon name="lucide:wifi-off" class="w-7 h-7 text-danger-500" />
      </div>
      <h3 class="text-base font-semibold text-heading mb-1">Gagal Memuat Data</h3>
      <p class="text-sm text-muted mb-5">{{ error }}</p>
      <UButton variant="primary" size="sm" @click="fetchProducts(currentPage)">
        <Icon name="lucide:refresh-cw" class="w-3.5 h-3.5 mr-1.5" />
        Coba Lagi
      </UButton>
    </div>

    <!-- Empty -->
    <div v-else-if="!products.length" class="relative overflow-hidden rounded-2xl border border-border bg-surface p-10 text-center">
      <div class="absolute inset-0 bg-gradient-to-br from-success-50/40 via-transparent to-primary-50/30 pointer-events-none" />
      <div class="relative">
        <div class="w-20 h-20 rounded-3xl bg-success-50 flex items-center justify-center mx-auto mb-5 shadow-sm">
          <Icon name="lucide:package" class="w-10 h-10 text-success-400" />
        </div>
        <h3 class="text-lg font-bold text-heading mb-2">Belum Ada Produk</h3>
        <p class="text-sm text-muted mb-6 max-w-sm mx-auto">
          Temukan berbagai produk digital berkualitas untuk mendukung kegiatan belajar mengajar.
        </p>
        <NuxtLink to="/products">
          <UButton variant="primary" size="md">
            <Icon name="lucide:shopping-bag" class="w-4 h-4 mr-1.5" />
            Jelajahi Produk
          </UButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Product Grid -->
    <template v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        <div
          v-for="item in products"
          :key="item.id"
          class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-medium hover:border-primary-200 hover:-translate-y-0.5 transition-all duration-300"
        >
          <!-- Thumbnail -->
          <div class="relative aspect-[4/3] overflow-hidden bg-surface-sunken">
            <img
              v-if="item.thumbnail_url"
              :src="item.thumbnail_url"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-success-50 to-primary-50/50">
              <Icon name="lucide:package" class="w-10 h-10 text-success-300" />
            </div>

            <!-- File type badge -->
            <span
              v-if="item.file_info?.type"
              :class="['absolute top-3 left-3 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider', fileTypeColor(item.file_info.type)]"
            >
              {{ item.file_info.type }}
            </span>
          </div>

          <!-- Content -->
          <div class="flex-1 flex flex-col p-4">
            <div v-if="item.category" class="mb-1.5">
              <span class="text-[11px] text-primary-600 font-semibold uppercase tracking-wide">
                {{ item.category.name }}
              </span>
            </div>

            <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 leading-snug">
              {{ item.title }}
            </h3>

            <p v-if="item.purchase_date_formatted" class="text-xs text-muted flex items-center gap-1.5 mb-3">
              <Icon name="lucide:calendar-check" class="w-3.5 h-3.5" />
              Dibeli {{ item.purchase_date_formatted }}
            </p>

            <!-- Download button -->
            <div class="mt-auto pt-3 border-t border-border-muted">
              <UButton
                v-if="item.download_url"
                variant="primary"
                size="sm"
                block
                :loading="isDownloading === item.product_id"
                :disabled="isDownloading === item.product_id"
                @click="handleDownload(item)"
              >
                <Icon name="lucide:download" class="w-3.5 h-3.5 mr-1.5" />
                Download File
              </UButton>
              <div v-else class="flex items-center justify-center gap-1.5 py-1.5 text-xs text-muted">
                <Icon name="lucide:alert-circle" class="w-3.5 h-3.5" />
                File tidak tersedia
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-center gap-1.5 mt-8">
        <UButton
          variant="outline"
          size="sm"
          :disabled="currentPage <= 1"
          @click="fetchProducts(currentPage - 1)"
        >
          <Icon name="lucide:chevron-left" class="w-4 h-4" />
        </UButton>
        <template v-for="p in totalPages" :key="p">
          <button
            v-if="p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)"
            type="button"
            class="w-9 h-9 rounded-xl text-xs font-semibold transition-all duration-200"
            :class="p === currentPage
              ? 'bg-primary-500 text-white shadow-sm'
              : 'text-muted hover:bg-surface-muted'"
            @click="fetchProducts(p)"
          >
            {{ p }}
          </button>
          <span
            v-else-if="p === currentPage - 2 || p === currentPage + 2"
            class="px-1 text-muted text-xs"
          >
            ...
          </span>
        </template>
        <UButton
          variant="outline"
          size="sm"
          :disabled="currentPage >= totalPages"
          @click="fetchProducts(currentPage + 1)"
        >
          <Icon name="lucide:chevron-right" class="w-4 h-4" />
        </UButton>
      </div>
    </template>
  </div>
</template>

