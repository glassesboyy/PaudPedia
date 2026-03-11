<script setup lang="ts">
/**
 * Product Detail Page
 * Route: /products/:slug
 *
 * FR-PR-P02: Display full product detail including:
 *  - description, price, file type, file size
 */
import DOMPurify from 'isomorphic-dompurify'
import type { Product, ProductDetail } from '~~/types'

const route = useRoute()
const slug = route.params.slug as string

// ─── State ───
const product = ref<ProductDetail | null>(null)
const relatedProducts = ref<Product[]>([])
const loading = ref(true)
const error = ref(false)

// ─── Fetch ───
async function fetchProduct() {
  loading.value = true
  error.value = false
  try {
    const { productService } = await import('~~/services')
    const res = await productService.getBySlug(slug)
    if (res.success && res.data) {
      product.value = res.data

      // Set SEO from product data
      useSeo({
        title: product.value.title,
        description: product.value.description
          ? stripHtml(product.value.description).slice(0, 160)
          : 'Detail produk digital di PaudPedia',
        image: product.value.thumbnail_url || undefined,
      })

      // Fetch related products from the same category
      fetchRelated()
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

async function fetchRelated() {
  if (!product.value?.category?.slug) return
  try {
    const { productService } = await import('~~/services')
    const res = await productService.getList({
      category: product.value.category.slug,
      per_page: 5,
    })
    if (res.success) {
      // Exclude current product
      relatedProducts.value = res.data.filter(p => p.id !== product.value?.id).slice(0, 4)
    }
  }
  catch {
    // Non-critical — silently fail
  }
}

onMounted(fetchProduct)

// ─── Helpers ───
function stripHtml(html: string): string {
  return html.replace(/<[^>]*>/g, '')
}

function formatPrice(price: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}

function formatDate(dateStr: string): string {
  try {
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    })
  }
  catch {
    return dateStr
  }
}

function shareProduct() {
  if (navigator.share) {
    navigator.share({
      title: product.value?.title,
      text: product.value?.description ? stripHtml(product.value.description).slice(0, 100) : '',
      url: window.location.href,
    })
  }
  else {
    navigator.clipboard.writeText(window.location.href)
  }
}

// ─── Sanitize HTML content ───
const sanitizedDescription = computed(() => {
  if (!product.value?.description) return ''
  return DOMPurify.sanitize(product.value.description, {
    ADD_TAGS: ['iframe'],
    ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'scrolling', 'target'],
  })
})

const isHtmlDescription = computed(() => {
  if (!product.value?.description) return false
  return /<[a-z][\s\S]*>/i.test(product.value.description)
})
</script>

<template>
  <div>
    <!-- Loading State -->
    <SkeletonDetailContent v-if="loading" variant="article" />

    <!-- Error State -->
    <div v-else-if="error" class="bg-surface">
      <div class="container py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-warning-50 flex items-center justify-center mx-auto mb-5">
          <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500" />
        </div>
        <h2 class="text-xl font-bold text-heading mb-2">Produk Tidak Ditemukan</h2>
        <p class="text-sm text-body mb-6">Produk yang Anda cari tidak tersedia atau telah dihapus.</p>
        <NuxtLink
          to="/products"
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
        >
          <Icon name="lucide:arrow-left" class="w-4 h-4" />
          Kembali ke Daftar Produk
        </NuxtLink>
      </div>
    </div>

    <!-- Product Content -->
    <template v-else-if="product">
      <!-- Hero / Header -->
      <section class="bg-gradient-to-br from-primary-50 via-surface to-success-50/30">
        <div class="container pt-8 pb-12 sm:pt-10 sm:pb-16">
          <div class="max-w-5xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs text-muted mb-6">
              <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <NuxtLink to="/products" class="hover:text-primary-600 transition-colors">Produk</NuxtLink>
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <span v-if="product.category" class="text-primary-600 font-medium">{{ product.category.name }}</span>
            </nav>

            <!-- Main layout: 2-col on desktop -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
              <!-- Left: Product image -->
              <div class="relative">
                <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-surface-sunken shadow-card">
                  <img
                    v-if="product.thumbnail_url"
                    :src="product.thumbnail_url"
                    :alt="product.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center bg-primary-50/50">
                    <Icon name="lucide:package" class="w-20 h-20 text-primary-200" />
                  </div>
                </div>

                <!-- Discount badge -->
                <span
                  v-if="product.has_discount && product.discount_percentage"
                  class="absolute top-4 right-4 px-3 py-1.5 rounded-xl text-sm font-bold bg-danger-500 text-white shadow-lg"
                >
                  -{{ product.discount_percentage }}%
                </span>
              </div>

              <!-- Right: Product info -->
              <div class="flex flex-col">
                <!-- Category -->
                <NuxtLink
                  v-if="product.category"
                  :to="`/products?category=${product.category.slug}`"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700 hover:bg-primary-200 transition-colors self-start mb-4"
                >
                  {{ product.category.name }}
                </NuxtLink>

                <!-- Title -->
                <h1 class="text-2xl sm:text-3xl font-bold text-heading leading-tight mb-4">
                  {{ product.title }}
                </h1>

                <!-- Price -->
                <div class="flex items-baseline gap-3 mb-6">
                  <span
                    class="text-3xl font-bold"
                    :class="product.price === 0 ? 'text-success-600' : 'text-primary-600'"
                  >
                    {{ product.price === 0 ? 'Gratis' : formatPrice(product.price) }}
                  </span>
                  <span
                    v-if="product.has_discount && product.original_price"
                    class="text-lg text-muted line-through"
                  >
                    {{ formatPrice(product.original_price) }}
                  </span>
                </div>

                <!-- File info card -->
                <div class="bg-surface rounded-xl border border-border p-5 mb-6">
                  <h3 class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Informasi File</h3>
                  <div class="grid grid-cols-2 gap-4">
                    <!-- File type -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:file-text" class="w-5 h-5 text-primary-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Tipe File</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ product.file_info?.type || 'Digital' }}
                        </p>
                      </div>
                    </div>

                    <!-- File size -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-success-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:hard-drive" class="w-5 h-5 text-success-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Ukuran</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ product.file_info?.size_formatted || '-' }}
                        </p>
                      </div>
                    </div>

                    <!-- Category -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-secondary-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:tag" class="w-5 h-5 text-secondary-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Kategori</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ product.category?.name || '-' }}
                        </p>
                      </div>
                    </div>

                    <!-- Date -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-warning-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:calendar" class="w-5 h-5 text-warning-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Dipublikasikan</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ formatDate(product.created_at) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Product Description -->
      <section class="bg-surface">
        <div class="container py-8 sm:py-12">
          <div class="max-w-5xl mx-auto">
            <!-- Section heading -->
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border/40">
              <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:file-text" class="w-5 h-5 text-primary-600" />
              </div>
              <h2 class="text-xl sm:text-2xl font-bold text-heading">Deskripsi Produk</h2>
            </div>

            <!-- HTML content -->
            <div
              v-if="isHtmlDescription"
              class="product-content"
              v-html="sanitizedDescription"
            />

            <!-- Plain text content -->
            <div v-else-if="product.description" class="product-content">
              <p class="whitespace-pre-line">{{ product.description }}</p>
            </div>

            <!-- No description -->
            <div v-else class="text-center py-10">
              <Icon name="lucide:file-x" class="w-10 h-10 text-muted/30 mx-auto mb-3" />
              <p class="text-sm text-muted">Deskripsi belum tersedia untuk produk ini.</p>
            </div>

            <!-- Footer actions -->
            <div class="mt-8 pt-6 border-t border-border/40 flex items-center justify-between">
              <NuxtLink
                to="/products"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors"
              >
                <Icon name="lucide:arrow-left" class="w-4 h-4" />
                Kembali ke Daftar Produk
              </NuxtLink>

              <button
                type="button"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors"
                @click="shareProduct"
              >
                <Icon name="lucide:share-2" class="w-4 h-4" />
                Bagikan
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Related Products -->
      <section v-if="relatedProducts.length > 0" class="bg-gradient-to-b from-surface to-primary-50/20">
        <div class="container py-12 sm:py-16">
          <div class="max-w-5xl mx-auto">
            <!-- Section header -->
            <div class="flex items-center gap-3 mb-8">
              <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:package" class="w-5 h-5 text-primary-600" />
              </div>
              <div>
                <h2 class="text-xl sm:text-2xl font-bold text-heading">Produk Terkait</h2>
                <p class="text-sm text-muted">Produk lain dalam kategori yang sama</p>
              </div>
            </div>

            <!-- Related products grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
              <ProductCard
                v-for="related in relatedProducts"
                :key="related.id"
                :product="related"
              />
            </div>

            <!-- See all products -->
            <div class="text-center mt-8">
              <NuxtLink
                to="/products"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-border text-sm font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all"
              >
                Lihat Semua Produk
                <Icon name="lucide:arrow-right" class="w-4 h-4" />
              </NuxtLink>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
/* ══════════════════════════════════════════════════════════════════
   Product Content — Typography & Element Styling
   Reuses article-content patterns for consistent rich content display.
   ══════════════════════════════════════════════════════════════════ */

/* ── Base ──────────────────────────────────────────────────────── */
.product-content {
  font-size: 1.0625rem;
  line-height: 1.8;
  color: rgb(var(--color-body));
  word-break: break-word;
  overflow-wrap: break-word;
}

/* ── Headings ─────────────────────────────────────────────────── */
.product-content :deep(h1) {
  font-size: 1.875rem;
  line-height: 1.3;
  font-weight: 700;
  color: rgb(var(--color-heading));
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #eff6ff;
}

.product-content :deep(h2) {
  font-size: 1.5rem;
  line-height: 1.35;
  font-weight: 700;
  color: rgb(var(--color-heading));
  margin-top: 2.25rem;
  margin-bottom: 0.75rem;
  padding-bottom: 0.375rem;
  border-bottom: 1px solid rgb(var(--color-border-muted));
}

.product-content :deep(h3) {
  font-size: 1.25rem;
  line-height: 1.4;
  font-weight: 600;
  color: rgb(var(--color-heading));
  margin-top: 2rem;
  margin-bottom: 0.625rem;
}

.product-content :deep(:first-child) {
  margin-top: 0;
}

/* ── Paragraph ────────────────────────────────────────────────── */
.product-content :deep(p) {
  margin-bottom: 1.25rem;
  color: rgb(var(--color-body));
  line-height: 1.8;
}

.product-content :deep(p:last-child) {
  margin-bottom: 0;
}

/* ── Inline Formatting ────────────────────────────────────────── */
.product-content :deep(strong),
.product-content :deep(b) {
  font-weight: 600;
  color: rgb(var(--color-heading));
}

.product-content :deep(em),
.product-content :deep(i) {
  font-style: italic;
}

.product-content :deep(u) {
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-color: #93c5fd;
}

/* ── Links ────────────────────────────────────────────────────── */
.product-content :deep(a) {
  color: #2563eb;
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-color: #bfdbfe;
  font-weight: 500;
  transition: color 0.15s, text-decoration-color 0.15s;
}

.product-content :deep(a:hover) {
  color: #1d4ed8;
  text-decoration-color: #2563eb;
}

/* ── Blockquote ───────────────────────────────────────────────── */
.product-content :deep(blockquote) {
  position: relative;
  margin: 1.75rem 0;
  padding: 1rem 1.5rem;
  border-left: 4px solid #3b82f6;
  background: #eff6ff;
  border-radius: 0 0.75rem 0.75rem 0;
  color: rgb(var(--color-heading));
  font-style: italic;
}

.product-content :deep(blockquote p) {
  margin-bottom: 0.5rem;
  color: inherit;
}

.product-content :deep(blockquote p:last-child) {
  margin-bottom: 0;
}

/* ── Lists ────────────────────────────────────────────────────── */
.product-content :deep(ul) {
  list-style-type: disc;
  margin: 1.25rem 0;
  padding-left: 1.75rem;
}

.product-content :deep(ol) {
  list-style-type: decimal;
  margin: 1.25rem 0;
  padding-left: 1.75rem;
}

.product-content :deep(li) {
  margin-bottom: 0.5rem;
  padding-left: 0.25rem;
  line-height: 1.7;
  color: rgb(var(--color-body));
}

.product-content :deep(li::marker) {
  color: #60a5fa;
}

/* ── Images ───────────────────────────────────────────────────── */
.product-content :deep(img) {
  max-width: 100%;
  height: auto;
  margin: 1.75rem auto;
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: block;
}

/* ── Tables ───────────────────────────────────────────────────── */
.product-content :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1.75rem 0;
  font-size: 0.9375rem;
  overflow-x: auto;
  display: block;
}

.product-content :deep(thead) {
  background: #eff6ff;
}

.product-content :deep(th) {
  padding: 0.75rem 1rem;
  text-align: left;
  font-weight: 600;
  color: rgb(var(--color-heading));
  border-bottom: 2px solid #dbeafe;
}

.product-content :deep(td) {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgb(var(--color-border-muted));
  color: rgb(var(--color-body));
}

/* ── Horizontal Rule ──────────────────────────────────────────── */
.product-content :deep(hr) {
  margin: 2.5rem 0;
  border: none;
  height: 1px;
  background: linear-gradient(to right, transparent, rgb(var(--color-border)), transparent);
}

/* ── Responsive ───────────────────────────────────────────────── */
@media (max-width: 640px) {
  .product-content {
    font-size: 1rem;
  }

  .product-content :deep(h1) {
    font-size: 1.5rem;
    margin-top: 2rem;
  }

  .product-content :deep(h2) {
    font-size: 1.25rem;
    margin-top: 1.75rem;
  }

  .product-content :deep(h3) {
    font-size: 1.125rem;
    margin-top: 1.5rem;
  }

  .product-content :deep(img) {
    border-radius: 0.5rem;
    margin: 1.25rem auto;
  }
}
</style>
