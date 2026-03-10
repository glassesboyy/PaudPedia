<script setup lang="ts">
/**
 * Article Detail Page
 * Route: /articles/:slug
 *
 * FR-AR-P02: Read full article without login
 * FR-AR-P07: Related articles navigation
 */
import DOMPurify from 'isomorphic-dompurify'
import type { Article, ArticleDetail } from '~~/types'

const route = useRoute()
const slug = route.params.slug as string

// ─── State ───
const article = ref<ArticleDetail | null>(null)
const relatedArticles = ref<Article[]>([])
const loading = ref(true)
const error = ref(false)

// ─── Fetch ───
async function fetchArticle() {
  loading.value = true
  error.value = false
  try {
    const { articleService } = await import('~~/services')
    const res = await articleService.getBySlug(slug)
    if (res.success && res.data) {
      article.value = res.data.article
      relatedArticles.value = res.data.related_articles || []

      // Set SEO from article data
      useSeo({
        title: article.value.meta?.title || article.value.title,
        description: article.value.meta?.description || article.value.excerpt,
        image: article.value.featured_image_url || undefined,
      })
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

onMounted(fetchArticle)

// ─── Helpers ───
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

function formatDateShort(dateStr: string): string {
  try {
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    })
  }
  catch {
    return dateStr
  }
}

function getImageUrl(a: Article | ArticleDetail): string | null {
  return a.featured_image_url || (a as any).thumbnail_url || null
}

function normalizeTags(tags: unknown): string[] {
  if (Array.isArray(tags)) return tags
  if (typeof tags === 'string' && tags.trim()) return tags.split(',').map(t => t.trim())
  return []
}

function shareArticle() {
  if (navigator.share) {
    navigator.share({
      title: article.value?.title,
      text: article.value?.excerpt,
      url: window.location.href,
    })
  }
  else {
    navigator.clipboard.writeText(window.location.href)
  }
}

// ─── Sanitize HTML content ───
const sanitizedContent = computed(() => {
  if (!article.value?.content) return ''
  return DOMPurify.sanitize(article.value.content, {
    ADD_TAGS: ['iframe'],
    ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'scrolling', 'target'],
  })
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
        <h2 class="text-xl font-bold text-heading mb-2">Artikel Tidak Ditemukan</h2>
        <p class="text-sm text-body mb-6">Artikel yang Anda cari tidak tersedia atau telah dihapus.</p>
        <NuxtLink
          to="/articles"
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
        >
          <Icon name="lucide:arrow-left" class="w-4 h-4" />
          Kembali ke Daftar Artikel
        </NuxtLink>
      </div>
    </div>

    <!-- Article Content -->
    <template v-else-if="article">
      <!-- Hero / Header -->
      <section class="bg-gradient-to-br from-primary-50 via-surface to-secondary-50/30">
        <div class="container pt-8 pb-12 sm:pt-10 sm:pb-16">
          <div class="max-w-3xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs text-muted mb-6">
              <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <NuxtLink to="/articles" class="hover:text-primary-600 transition-colors">Artikel</NuxtLink>
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <span v-if="article.category" class="text-primary-600 font-medium">{{ article.category.name }}</span>
            </nav>

            <!-- Category & Tags -->
            <div class="flex flex-wrap items-center gap-2 mb-4">
              <NuxtLink
                v-if="article.category"
                :to="`/articles?category=${article.category.slug}`"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700 hover:bg-primary-200 transition-colors"
              >
                {{ article.category.name }}
              </NuxtLink>
              <span
                v-if="article.is_featured"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-warning-100 text-warning-700"
              >
                <Icon name="lucide:star" class="w-3 h-3" />
                Featured
              </span>
            </div>

            <!-- Title -->
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-heading leading-tight mb-5">
              {{ article.title }}
            </h1>

            <!-- Excerpt -->
            <p v-if="article.excerpt" class="text-base sm:text-lg text-body/80 leading-relaxed mb-6">
              {{ article.excerpt }}
            </p>

            <!-- Author & Meta -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-border/40">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                  <span class="text-sm font-bold text-primary-600">
                    {{ article.author?.name?.charAt(0) || '?' }}
                  </span>
                </div>
                <div>
                  <p class="text-sm font-semibold text-heading">{{ article.author?.name || 'Unknown' }}</p>
                  <div class="flex items-center gap-3 text-xs text-muted">
                    <span>{{ article.published_date || formatDate(article.published_at) }}</span>
                    <span class="flex items-center gap-1">
                      <Icon name="lucide:clock" class="w-3 h-3" />
                      {{ article.reading_time || 1 }} min baca
                    </span>
                    <span v-if="article.view_count" class="flex items-center gap-1">
                      <Icon name="lucide:eye" class="w-3 h-3" />
                      {{ article.view_count }}x dibaca
                    </span>
                  </div>
                </div>
              </div>

              <!-- Share -->
              <button
                type="button"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border text-xs font-medium text-body hover:border-primary-300 hover:text-primary-600 transition-all self-start"
                @click="shareArticle"
              >
                <Icon name="lucide:share-2" class="w-4 h-4" />
                Bagikan
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Article Body -->
      <section class="bg-surface">
        <div class="container py-8 sm:py-12">
          <div class="max-w-3xl mx-auto">
            <!-- Cover Image -->
            <div v-if="getImageUrl(article)" class="mb-8 sm:mb-10 -mt-4">
              <img
                :src="getImageUrl(article)!"
                :alt="article.title"
                class="w-full rounded-2xl shadow-card object-cover max-h-[500px]"
              />
            </div>

            <!-- Content (HTML from backend — sanitized) -->
            <div
              class="article-content"
              v-html="sanitizedContent"
            />

            <!-- Tags -->
            <div v-if="normalizeTags(article.tags).length" class="mt-10 pt-6 border-t border-border/40">
              <div class="flex items-center gap-2 flex-wrap">
                <Icon name="lucide:tag" class="w-4 h-4 text-muted" />
                <NuxtLink
                  v-for="tag in normalizeTags(article.tags)"
                  :key="tag"
                  :to="`/articles?tag=${tag}`"
                  class="px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600 border border-primary-100 hover:bg-primary-100 hover:border-primary-200 transition-colors"
                >
                  #{{ tag }}
                </NuxtLink>
              </div>
            </div>

            <!-- Article footer actions -->
            <div class="mt-8 pt-6 border-t border-border/40 flex items-center justify-between">
              <NuxtLink
                to="/articles"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors"
              >
                <Icon name="lucide:arrow-left" class="w-4 h-4" />
                Kembali ke Daftar Artikel
              </NuxtLink>

              <button
                type="button"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors"
                @click="shareArticle"
              >
                <Icon name="lucide:share-2" class="w-4 h-4" />
                Bagikan
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Related Articles -->
      <section v-if="relatedArticles.length > 0" class="bg-gradient-to-b from-surface to-primary-50/20">
        <div class="container py-12 sm:py-16">
          <div class="max-w-5xl mx-auto">
            <!-- Section header -->
            <div class="flex items-center gap-3 mb-8">
              <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:newspaper" class="w-5 h-5 text-primary-600" />
              </div>
              <div>
                <h2 class="text-xl sm:text-2xl font-bold text-heading">Artikel Terkait</h2>
                <p class="text-sm text-muted">Artikel lain yang mungkin menarik untuk Anda</p>
              </div>
            </div>

            <!-- Related articles grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
              <NuxtLink
                v-for="related in relatedArticles.slice(0, 4)"
                :key="related.id"
                :to="`/articles/${related.slug}`"
                class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-card hover:-translate-y-0.5 transition-all duration-300"
              >
                <!-- Image -->
                <div class="relative aspect-[16/10] overflow-hidden bg-primary-50/30">
                  <img
                    v-if="getImageUrl(related)"
                    :src="getImageUrl(related)!"
                    :alt="related.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <Icon name="lucide:newspaper" class="w-8 h-8 text-muted/30" />
                  </div>
                  <span
                    v-if="related.category"
                    class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/90 text-primary-700 backdrop-blur-sm"
                  >
                    {{ related.category.name }}
                  </span>
                </div>

                <!-- Content -->
                <div class="flex-1 flex flex-col p-4">
                  <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors leading-snug">
                    {{ related.title }}
                  </h3>
                  <p class="text-xs text-body/70 line-clamp-2 mb-3">{{ related.excerpt }}</p>
                  <div class="mt-auto flex items-center justify-between text-[11px] text-muted">
                    <span>{{ related.published_date || formatDateShort(related.published_at) }}</span>
                    <span class="flex items-center gap-1">
                      <Icon name="lucide:clock" class="w-3 h-3" />
                      {{ related.reading_time || 1 }} min
                    </span>
                  </div>
                </div>
              </NuxtLink>
            </div>

            <!-- See all articles -->
            <div class="text-center mt-8">
              <NuxtLink
                to="/articles"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-border text-sm font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all"
              >
                Lihat Semua Artikel
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
   Article Content — Typography & Element Styling
   Handles all HTML output from Filament RichEditor:
   h1-h3, p, strong, em, u, s, a, blockquote, pre, code,
   ul, ol, li, img, hr, text-align, tables
   ══════════════════════════════════════════════════════════════════ */

/* ── Base ──────────────────────────────────────────────────────── */
.article-content {
  font-size: 1.0625rem;           /* ~17px — optimal reading size */
  line-height: 1.8;
  color: rgb(var(--color-body));
  word-break: break-word;
  overflow-wrap: break-word;
}

/* ── Headings ─────────────────────────────────────────────────── */
.article-content :deep(h1) {
  font-size: 1.875rem;            /* 30px */
  line-height: 1.3;
  font-weight: 700;
  color: rgb(var(--color-heading));
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #eff6ff; /* primary-50 */
}

.article-content :deep(h2) {
  font-size: 1.5rem;              /* 24px */
  line-height: 1.35;
  font-weight: 700;
  color: rgb(var(--color-heading));
  margin-top: 2.25rem;
  margin-bottom: 0.75rem;
  padding-bottom: 0.375rem;
  border-bottom: 1px solid rgb(var(--color-border-muted));
}

.article-content :deep(h3) {
  font-size: 1.25rem;             /* 20px */
  line-height: 1.4;
  font-weight: 600;
  color: rgb(var(--color-heading));
  margin-top: 2rem;
  margin-bottom: 0.625rem;
}

/* Remove top margin on first heading */
.article-content :deep(:first-child) {
  margin-top: 0;
}

/* ── Paragraph ────────────────────────────────────────────────── */
.article-content :deep(p) {
  margin-bottom: 1.25rem;
  color: rgb(var(--color-body));
  line-height: 1.8;
}

.article-content :deep(p:last-child) {
  margin-bottom: 0;
}

/* ── Inline Formatting ────────────────────────────────────────── */
.article-content :deep(strong),
.article-content :deep(b) {
  font-weight: 600;
  color: rgb(var(--color-heading));
}

.article-content :deep(em),
.article-content :deep(i) {
  font-style: italic;
}

.article-content :deep(u) {
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-color: #93c5fd; /* primary-300 */
}

.article-content :deep(s),
.article-content :deep(del) {
  text-decoration: line-through;
  opacity: 0.7;
}

/* ── Links ────────────────────────────────────────────────────── */
.article-content :deep(a) {
  color: #2563eb;                 /* primary-600 */
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-color: #bfdbfe; /* primary-200 */
  font-weight: 500;
  transition: color 0.15s, text-decoration-color 0.15s;
}

.article-content :deep(a:hover) {
  color: #1d4ed8;                 /* primary-700 */
  text-decoration-color: #2563eb; /* primary-600 */
}

/* ── Blockquote ───────────────────────────────────────────────── */
.article-content :deep(blockquote) {
  position: relative;
  margin: 1.75rem 0;
  padding: 1rem 1.5rem;
  border-left: 4px solid #3b82f6; /* primary-500 */
  background: #eff6ff;            /* primary-50 */
  border-radius: 0 0.75rem 0.75rem 0;
  color: rgb(var(--color-heading));
  font-style: italic;
}

.article-content :deep(blockquote p) {
  margin-bottom: 0.5rem;
  color: inherit;
}

.article-content :deep(blockquote p:last-child) {
  margin-bottom: 0;
}

/* ── Lists ────────────────────────────────────────────────────── */
.article-content :deep(ul) {
  list-style-type: disc;
  margin: 1.25rem 0;
  padding-left: 1.75rem;
}

.article-content :deep(ol) {
  list-style-type: decimal;
  margin: 1.25rem 0;
  padding-left: 1.75rem;
}

.article-content :deep(li) {
  margin-bottom: 0.5rem;
  padding-left: 0.25rem;
  line-height: 1.7;
  color: rgb(var(--color-body));
}

.article-content :deep(li::marker) {
  color: #60a5fa;                 /* primary-400 */
}

.article-content :deep(li > ul),
.article-content :deep(li > ol) {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

/* ── Inline Code ──────────────────────────────────────────────── */
.article-content :deep(code) {
  font-family: 'Cascadia Code', 'Fira Code', 'JetBrains Mono', 'SF Mono', Consolas, monospace;
  font-size: 0.875em;
  background: #eff6ff;            /* primary-50 */
  color: #1d4ed8;                 /* primary-700 */
  padding: 0.15em 0.45em;
  border-radius: 0.375rem;
  border: 1px solid #dbeafe;      /* primary-100 */
  word-break: break-word;
}

/* ── Code Block (pre > code) ──────────────────────────────────── */
.article-content :deep(pre) {
  margin: 1.75rem 0;
  padding: 1.25rem 1.5rem;
  background: #1e293b;            /* slate-800 */
  border-radius: 0.75rem;
  overflow-x: auto;
  border: 1px solid #334155;      /* slate-700 */
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.article-content :deep(pre code) {
  font-family: 'Cascadia Code', 'Fira Code', 'JetBrains Mono', 'SF Mono', Consolas, monospace;
  font-size: 0.875rem;
  line-height: 1.7;
  background: transparent;
  color: #e2e8f0;                 /* slate-200 */
  padding: 0;
  border-radius: 0;
  border: none;
  white-space: pre;
  word-break: normal;
}

/* ── Horizontal Rule ──────────────────────────────────────────── */
.article-content :deep(hr) {
  margin: 2.5rem 0;
  border: none;
  height: 1px;
  background: linear-gradient(
    to right,
    transparent,
    rgb(var(--color-border)),
    transparent
  );
}

/* ── Images ───────────────────────────────────────────────────── */
.article-content :deep(img) {
  max-width: 100%;
  height: auto;
  margin: 1.75rem auto;
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: block;
}

/* ── Tables ───────────────────────────────────────────────────── */
.article-content :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1.75rem 0;
  font-size: 0.9375rem;
  overflow-x: auto;
  display: block;
}

.article-content :deep(thead) {
  background: #eff6ff;            /* primary-50 */
}

.article-content :deep(th) {
  padding: 0.75rem 1rem;
  text-align: left;
  font-weight: 600;
  color: rgb(var(--color-heading));
  border-bottom: 2px solid #dbeafe; /* primary-100 */
}

.article-content :deep(td) {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgb(var(--color-border-muted));
  color: rgb(var(--color-body));
}

.article-content :deep(tr:hover td) {
  background: rgba(239, 246, 255, 0.5); /* primary-50/50 */
}

/* ── Text Alignment (from RichEditor) ─────────────────────────── */
.article-content :deep([style*="text-align: center"]),
.article-content :deep([style*="text-align:center"]) {
  text-align: center;
}

.article-content :deep([style*="text-align: right"]),
.article-content :deep([style*="text-align:right"]) {
  text-align: right;
}

.article-content :deep([style*="text-align: left"]),
.article-content :deep([style*="text-align:left"]) {
  text-align: left;
}

/* ── Figure / Caption ─────────────────────────────────────────── */
.article-content :deep(figure) {
  margin: 1.75rem 0;
  text-align: center;
}

.article-content :deep(figcaption) {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: rgb(var(--color-muted));
  font-style: italic;
}

/* ── Responsive ───────────────────────────────────────────────── */
@media (max-width: 640px) {
  .article-content {
    font-size: 1rem;
  }

  .article-content :deep(h1) {
    font-size: 1.5rem;
    margin-top: 2rem;
  }

  .article-content :deep(h2) {
    font-size: 1.25rem;
    margin-top: 1.75rem;
  }

  .article-content :deep(h3) {
    font-size: 1.125rem;
    margin-top: 1.5rem;
  }

  .article-content :deep(pre) {
    padding: 1rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
  }

  .article-content :deep(blockquote) {
    padding: 0.75rem 1rem;
    margin: 1.25rem 0;
  }

  .article-content :deep(img) {
    border-radius: 0.5rem;
    margin: 1.25rem auto;
  }
}
</style>
