<script setup lang="ts">
/**
 * Course Detail Page
 * Route: /courses/:slug
 *
 * FR-CR-P02: Display full course detail including:
 *  - description, price, level, duration, mentor, syllabus (modules + lessons)
 */
import DOMPurify from 'isomorphic-dompurify'
import type { Course, CourseDetail } from '~~/types'

const route = useRoute()
const slug = route.params.slug as string

// ─── Cart ───
const { addToCart, hasItem, isAddingItem } = useCart()
const isInCart = computed(() => course.value ? hasItem(course.value.id, 'course') : false)
const isAdding = computed(() => course.value ? isAddingItem(course.value.id, 'course') : false)

function handleAddToCart() {
  if (!course.value || isInCart.value) return
  addToCart({
    id: course.value.id,
    type: 'course',
    name: course.value.title,
    slug: course.value.slug,
    price: course.value.price,
    thumbnail: course.value.thumbnail_url || '',
  })
}

// ─── State ───
const course = ref<CourseDetail | null>(null)
const relatedCourses = ref<Course[]>([])
const loading = ref(true)
const error = ref(false)
const expandedModules = ref<Set<number>>(new Set())

// ─── Fetch ───
async function fetchCourse() {
  loading.value = true
  error.value = false
  try {
    const { courseService } = await import('~~/services')
    const res = await courseService.getBySlug(slug)
    if (res.success && res.data) {
      course.value = res.data

      // Expand first module by default
      if (course.value.modules?.length) {
        const firstModule = course.value.modules[0]
        if (firstModule) {
          expandedModules.value.add(firstModule.id)
        }
      }

      useSeo({
        title: course.value.title,
        description: course.value.description
          ? stripHtml(course.value.description).slice(0, 160)
          : 'Detail kursus online di PaudPedia',
        image: course.value.thumbnail_url || undefined,
      })

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
  if (!course.value?.category?.slug) return
  try {
    const { courseService } = await import('~~/services')
    const res = await courseService.getList({
      category: course.value.category.slug,
      per_page: 5,
    })
    if (res.success) {
      relatedCourses.value = res.data.filter(c => c.id !== course.value?.id).slice(0, 3)
    }
  }
  catch {
    // Non-critical
  }
}

onMounted(fetchCourse)

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

function shareCourse() {
  if (navigator.share) {
    navigator.share({
      title: course.value?.title,
      text: course.value?.description ? stripHtml(course.value.description).slice(0, 100) : '',
      url: window.location.href,
    })
  }
  else {
    navigator.clipboard.writeText(window.location.href)
  }
}

function toggleModule(moduleId: number) {
  if (expandedModules.value.has(moduleId)) {
    expandedModules.value.delete(moduleId)
  }
  else {
    expandedModules.value.add(moduleId)
  }
}

function expandAllModules() {
  if (!course.value?.modules) return
  course.value.modules.forEach(m => expandedModules.value.add(m.id))
}

function collapseAllModules() {
  expandedModules.value.clear()
}

const allExpanded = computed(() => {
  if (!course.value?.modules?.length) return false
  return course.value.modules.every(m => expandedModules.value.has(m.id))
})

// ─── Level display ───
const levelLabels: Record<string, string> = {
  beginner: 'Pemula',
  intermediate: 'Menengah',
  advanced: 'Lanjutan',
}

const levelColors: Record<string, string> = {
  beginner: 'bg-success-50 text-success-700 border-success-200',
  intermediate: 'bg-warning-50 text-warning-700 border-warning-200',
  advanced: 'bg-danger-50 text-danger-700 border-danger-200',
}

// ─── Lesson type icons ───
const lessonTypeIcons: Record<string, string> = {
  video: 'lucide:play-circle',
  pdf: 'lucide:file-text',
  text: 'lucide:book-open',
}

// ─── Computed ───
const totalLessons = computed(() => {
  if (!course.value?.modules) return 0
  return course.value.modules.reduce((sum, m) => sum + m.lessons.length, 0)
})

const totalDuration = computed(() => {
  if (!course.value?.modules) return 0
  return course.value.modules.reduce((sum, m) =>
    sum + m.lessons.reduce((lSum, l) => lSum + (l.duration_minutes || 0), 0), 0)
})

const sanitizedDescription = computed(() => {
  if (!course.value?.description) return ''
  return DOMPurify.sanitize(course.value.description, {
    ADD_TAGS: ['iframe'],
    ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'scrolling', 'target'],
  })
})

const isHtmlDescription = computed(() => {
  if (!course.value?.description) return false
  return /<[a-z][\s\S]*>/i.test(course.value.description)
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
        <h2 class="text-xl font-bold text-heading mb-2">Kursus Tidak Ditemukan</h2>
        <p class="text-sm text-body mb-6">Kursus yang Anda cari tidak tersedia atau telah dihapus.</p>
        <NuxtLink
          to="/courses"
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
        >
          <Icon name="lucide:arrow-left" class="w-4 h-4" />
          Kembali ke Daftar Kursus
        </NuxtLink>
      </div>
    </div>

    <!-- Course Content -->
    <template v-else-if="course">
      <!-- Hero / Header -->
      <section class="bg-gradient-to-br from-primary-50 via-surface to-secondary-50/30">
        <div class="container pt-8 pb-12 sm:pt-10 sm:pb-16">
          <div class="max-w-5xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs text-muted mb-6">
              <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <NuxtLink to="/courses" class="hover:text-primary-600 transition-colors">Kursus</NuxtLink>
              <Icon name="lucide:chevron-right" class="w-3 h-3" />
              <span v-if="course.category" class="text-primary-600 font-medium">{{ course.category.name }}</span>
            </nav>

            <!-- Main layout: 2-col on desktop -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
              <!-- Left: Course image -->
              <div class="relative">
                <div class="aspect-video rounded-2xl overflow-hidden bg-surface-sunken shadow-card">
                  <img
                    v-if="course.thumbnail_url"
                    :src="course.thumbnail_url"
                    :alt="course.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center bg-primary-50/50">
                    <Icon name="lucide:graduation-cap" class="w-20 h-20 text-primary-200" />
                  </div>
                </div>

                <!-- Discount badge -->
                <span
                  v-if="course.has_discount && course.discount_percentage"
                  class="absolute top-4 right-4 px-3 py-1.5 rounded-xl text-sm font-bold bg-danger-500 text-white shadow-lg"
                >
                  -{{ course.discount_percentage }}%
                </span>
              </div>

              <!-- Right: Course info -->
              <div class="flex flex-col">
                <!-- Category + Level -->
                <div class="flex items-center gap-2 mb-4">
                  <NuxtLink
                    v-if="course.category"
                    :to="`/courses?category=${course.category.slug}`"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700 hover:bg-primary-200 transition-colors"
                  >
                    {{ course.category.name }}
                  </NuxtLink>
                  <span
                    v-if="course.level"
                    :class="['px-3 py-1 rounded-full text-xs font-semibold border', levelColors[course.level] || 'bg-surface text-body border-border']"
                  >
                    {{ course.level_label || levelLabels[course.level] || course.level }}
                  </span>
                </div>

                <!-- Title -->
                <h1 class="text-2xl sm:text-3xl font-bold text-heading leading-tight mb-4">
                  {{ course.title }}
                </h1>

                <!-- Price -->
                <div class="flex items-baseline gap-3 mb-6">
                  <span
                    class="text-3xl font-bold"
                    :class="course.price === 0 ? 'text-success-600' : 'text-primary-600'"
                  >
                    {{ course.price === 0 ? 'Gratis' : formatPrice(course.price) }}
                  </span>
                  <span
                    v-if="course.has_discount && course.original_price"
                    class="text-lg text-muted line-through"
                  >
                    {{ formatPrice(course.original_price) }}
                  </span>
                </div>

                <!-- Course info card -->
                <div class="bg-surface rounded-xl border border-border p-5 mb-6">
                  <h3 class="text-xs font-semibold text-muted uppercase tracking-wide mb-3">Informasi Kursus</h3>
                  <div class="grid grid-cols-2 gap-4">
                    <!-- Level -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:signal" class="w-5 h-5 text-primary-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Tingkat</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ course.level_label || levelLabels[course.level || ''] || 'Semua Level' }}
                        </p>
                      </div>
                    </div>

                    <!-- Duration -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-success-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:clock" class="w-5 h-5 text-success-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Durasi</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ course.duration_hours ? `${course.duration_hours} jam` : '-' }}
                        </p>
                      </div>
                    </div>

                    <!-- Modules -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-secondary-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:layers" class="w-5 h-5 text-secondary-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Modul</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ course.modules_count }} modul · {{ totalLessons }} materi
                        </p>
                      </div>
                    </div>

                    <!-- Enrollments -->
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-warning-50 flex items-center justify-center flex-shrink-0">
                        <Icon name="lucide:users" class="w-5 h-5 text-warning-500" />
                      </div>
                      <div>
                        <p class="text-[11px] text-muted">Peserta</p>
                        <p class="text-sm font-semibold text-heading">
                          {{ course.enrollments_count ?? 0 }} terdaftar
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Mentor card -->
                <div v-if="course.mentor" class="bg-surface rounded-xl border border-border p-5">
                  <h3 class="text-xs font-semibold text-muted uppercase tracking-wide mb-4">Mentor</h3>
                  <div class="flex items-start gap-4">
                    <NuxtLink :to="`/mentors/${course.mentor.id}`" class="flex-shrink-0">
                      <div class="w-16 h-16 rounded-xl bg-primary-100 flex items-center justify-center overflow-hidden ring-2 ring-primary-100 hover:ring-primary-300 transition-all">
                        <img
                          v-if="course.mentor.photo_url"
                          :src="course.mentor.photo_url"
                          :alt="course.mentor.name"
                          class="w-full h-full object-cover"
                        />
                        <span v-else class="text-xl font-bold text-primary-600">
                          {{ course.mentor.name?.charAt(0)?.toUpperCase() }}
                        </span>
                      </div>
                    </NuxtLink>
                    <div class="min-w-0 flex-1">
                      <NuxtLink :to="`/mentors/${course.mentor.id}`" class="text-sm font-semibold text-heading hover:text-primary-600 transition-colors">
                        {{ course.mentor.name }}
                      </NuxtLink>
                      <p v-if="course.mentor.title" class="text-xs text-muted mt-0.5">{{ course.mentor.title }}</p>
                      <p v-if="course.mentor.bio" class="text-xs text-body mt-2 line-clamp-2 leading-relaxed">{{ course.mentor.bio }}</p>
                      <p v-else-if="course.mentor.expertise" class="text-xs text-body mt-2 line-clamp-2 leading-relaxed">{{ course.mentor.expertise }}</p>

                      <!-- Social media links -->
                      <div v-if="course.mentor.social_media && Object.keys(course.mentor.social_media).length" class="flex items-center gap-2 mt-3">
                        <a
                          v-for="(url, platform) in course.mentor.social_media"
                          :key="platform"
                          :href="String(url)"
                          target="_blank"
                          rel="noopener noreferrer"
                          class="w-7 h-7 rounded-lg bg-surface-sunken flex items-center justify-center text-muted hover:text-primary-600 hover:bg-primary-50 transition-colors"
                          :title="String(platform)"
                        >
                          <Icon
                            :name="platform === 'instagram' ? 'lucide:instagram' : platform === 'linkedin' ? 'lucide:linkedin' : platform === 'youtube' ? 'lucide:youtube' : platform === 'twitter' || platform === 'x' ? 'lucide:twitter' : platform === 'facebook' ? 'lucide:facebook' : 'lucide:globe'"
                            class="w-3.5 h-3.5"
                          />
                        </a>
                      </div>
                    </div>
                  </div>

                  <!-- View profile link -->
                  <NuxtLink
                    :to="`/mentors/${course.mentor.id}`"
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-border text-xs font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all"
                  >
                    <Icon name="lucide:user" class="w-3.5 h-3.5" />
                    Lihat Profil Mentor
                  </NuxtLink>
                </div>

                <!-- Add to cart button -->
                <div class="mt-6">
                  <button
                    v-if="course.price > 0"
                    type="button"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold transition-all shadow-sm"
                    :class="isInCart
                      ? 'bg-success-50 text-success-700 border border-success-200 cursor-default'
                      : 'bg-primary-600 text-white hover:bg-primary-700'"
                    :disabled="isInCart || isAdding"
                    @click="handleAddToCart"
                  >
                    <Icon v-if="isAdding" name="lucide:loader-2" class="w-4 h-4 animate-spin" />
                    <Icon v-else :name="isInCart ? 'lucide:check-circle' : 'lucide:shopping-cart'" class="w-4 h-4" />
                    {{ isAdding ? 'Menambahkan...' : isInCart ? 'Sudah di Keranjang' : 'Tambah ke Keranjang' }}
                  </button>
                  <NuxtLink
                    v-if="isInCart"
                    to="/cart"
                    class="mt-2 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-border text-xs font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all"
                  >
                    <Icon name="lucide:arrow-right" class="w-3.5 h-3.5" />
                    Lihat Keranjang
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Syllabus Section -->
      <section v-if="course.modules && course.modules.length > 0" class="bg-gradient-to-b from-surface to-primary-50/10">
        <div class="container py-8 sm:py-12">
          <div class="max-w-5xl mx-auto">
            <!-- Section heading -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-border/40">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                  <Icon name="lucide:list-tree" class="w-5 h-5 text-primary-600" />
                </div>
                <div>
                  <h2 class="text-xl sm:text-2xl font-bold text-heading">Silabus Kursus</h2>
                  <p class="text-xs text-muted">{{ course.modules.length }} modul · {{ totalLessons }} materi · {{ totalDuration }} menit</p>
                </div>
              </div>
              <button
                type="button"
                class="text-xs font-medium text-primary-600 hover:text-primary-700 transition-colors"
                @click="allExpanded ? collapseAllModules() : expandAllModules()"
              >
                {{ allExpanded ? 'Tutup Semua' : 'Buka Semua' }}
              </button>
            </div>

            <!-- Modules accordion -->
            <div class="space-y-3">
              <div
                v-for="(mod, mIdx) in course.modules"
                :key="mod.id"
                class="rounded-xl border border-border bg-surface overflow-hidden"
              >
                <!-- Module header -->
                <button
                  type="button"
                  class="w-full flex items-center gap-4 p-4 text-left hover:bg-primary-50/30 transition-colors"
                  @click="toggleModule(mod.id)"
                >
                  <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-primary-600">{{ mIdx + 1 }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-heading truncate">{{ mod.title }}</h3>
                    <p class="text-xs text-muted">{{ mod.lessons.length }} materi</p>
                  </div>
                  <Icon
                    name="lucide:chevron-down"
                    class="w-4 h-4 text-muted transition-transform duration-200 flex-shrink-0"
                    :class="{ 'rotate-180': expandedModules.has(mod.id) }"
                  />
                </button>

                <!-- Lessons list -->
                <div
                  v-if="expandedModules.has(mod.id)"
                  class="border-t border-border/40"
                >
                  <div
                    v-for="(lesson, lIdx) in mod.lessons"
                    :key="lesson.id"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-surface-sunken/50 transition-colors"
                    :class="{ 'border-t border-border/20': lIdx > 0 }"
                  >
                    <Icon
                      :name="lessonTypeIcons[lesson.type || ''] || 'lucide:file'"
                      class="w-4 h-4 text-muted flex-shrink-0"
                    />
                    <span class="flex-1 text-sm text-body truncate">{{ lesson.title }}</span>
                    <div class="flex items-center gap-2 flex-shrink-0">
                      <span
                        v-if="lesson.is_preview"
                        class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-success-50 text-success-700"
                      >
                        Preview
                      </span>
                      <span v-if="lesson.duration_minutes" class="text-xs text-muted">
                        {{ lesson.duration_minutes }} min
                      </span>
                    </div>
                  </div>

                  <!-- Empty lessons -->
                  <div v-if="mod.lessons.length === 0" class="px-4 py-6 text-center">
                    <p class="text-xs text-muted">Belum ada materi di modul ini.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Course Description -->
      <section class="bg-surface">
        <div class="container py-8 sm:py-12">
          <div class="max-w-5xl mx-auto">
            <!-- Section heading -->
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border/40">
              <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:file-text" class="w-5 h-5 text-primary-600" />
              </div>
              <h2 class="text-xl sm:text-2xl font-bold text-heading">Deskripsi Kursus</h2>
            </div>

            <!-- HTML content -->
            <div
              v-if="isHtmlDescription"
              class="course-content"
              v-html="sanitizedDescription"
            />

            <!-- Plain text content -->
            <div v-else-if="course.description" class="course-content">
              <p class="whitespace-pre-line">{{ course.description }}</p>
            </div>

            <!-- No description -->
            <div v-else class="text-center py-10">
              <Icon name="lucide:file-x" class="w-10 h-10 text-muted/30 mx-auto mb-3" />
              <p class="text-sm text-muted">Deskripsi belum tersedia untuk kursus ini.</p>
            </div>

            <!-- Footer actions -->
            <div class="mt-8 pt-6 border-t border-border/40 flex items-center justify-between">
              <NuxtLink
                to="/courses"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors"
              >
                <Icon name="lucide:arrow-left" class="w-4 h-4" />
                Kembali ke Daftar Kursus
              </NuxtLink>

              <button
                type="button"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-primary-600 transition-colors"
                @click="shareCourse"
              >
                <Icon name="lucide:share-2" class="w-4 h-4" />
                Bagikan
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Related Courses -->
      <section v-if="relatedCourses.length > 0" class="bg-gradient-to-b from-surface to-primary-50/20">
        <div class="container py-12 sm:py-16">
          <div class="max-w-5xl mx-auto">
            <!-- Section header -->
            <div class="flex items-center gap-3 mb-8">
              <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:graduation-cap" class="w-5 h-5 text-primary-600" />
              </div>
              <div>
                <h2 class="text-xl sm:text-2xl font-bold text-heading">Kursus Terkait</h2>
                <p class="text-sm text-muted">Kursus lain dalam kategori yang sama</p>
              </div>
            </div>

            <!-- Related courses grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
              <CourseCard
                v-for="related in relatedCourses"
                :key="related.id"
                :course="related"
              />
            </div>

            <!-- See all courses -->
            <div class="text-center mt-8">
              <NuxtLink
                to="/courses"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-border text-sm font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all"
              >
                Lihat Semua Kursus
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
   Course Content — Typography & Element Styling
   Reuses product-content patterns for consistent rich content display.
   ══════════════════════════════════════════════════════════════════ */

/* ── Base ──────────────────────────────────────────────────────── */
.course-content {
  font-size: 1.0625rem;
  line-height: 1.8;
  color: rgb(var(--color-body));
  word-break: break-word;
  overflow-wrap: break-word;
}

/* ── Headings ─────────────────────────────────────────────────── */
.course-content :deep(h1) {
  font-size: 1.875rem;
  line-height: 1.3;
  font-weight: 700;
  color: rgb(var(--color-heading));
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #eff6ff;
}

.course-content :deep(h2) {
  font-size: 1.5rem;
  line-height: 1.35;
  font-weight: 700;
  color: rgb(var(--color-heading));
  margin-top: 2.25rem;
  margin-bottom: 0.75rem;
  padding-bottom: 0.375rem;
  border-bottom: 1px solid rgb(var(--color-border-muted));
}

.course-content :deep(h3) {
  font-size: 1.25rem;
  line-height: 1.4;
  font-weight: 600;
  color: rgb(var(--color-heading));
  margin-top: 2rem;
  margin-bottom: 0.625rem;
}

.course-content :deep(:first-child) {
  margin-top: 0;
}

/* ── Paragraph ────────────────────────────────────────────────── */
.course-content :deep(p) {
  margin-bottom: 1.25rem;
  color: rgb(var(--color-body));
  line-height: 1.8;
}

.course-content :deep(p:last-child) {
  margin-bottom: 0;
}

/* ── Inline Formatting ────────────────────────────────────────── */
.course-content :deep(strong),
.course-content :deep(b) {
  font-weight: 600;
  color: rgb(var(--color-heading));
}

.course-content :deep(em),
.course-content :deep(i) {
  font-style: italic;
}

.course-content :deep(u) {
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-color: #93c5fd;
}

/* ── Links ────────────────────────────────────────────────────── */
.course-content :deep(a) {
  color: #2563eb;
  text-decoration: underline;
  text-underline-offset: 3px;
  text-decoration-color: #bfdbfe;
  font-weight: 500;
  transition: color 0.15s, text-decoration-color 0.15s;
}

.course-content :deep(a:hover) {
  color: #1d4ed8;
  text-decoration-color: #2563eb;
}

/* ── Blockquote ───────────────────────────────────────────────── */
.course-content :deep(blockquote) {
  position: relative;
  margin: 1.75rem 0;
  padding: 1rem 1.5rem;
  border-left: 4px solid #3b82f6;
  background: #eff6ff;
  border-radius: 0 0.75rem 0.75rem 0;
  color: rgb(var(--color-heading));
  font-style: italic;
}

.course-content :deep(blockquote p) {
  margin-bottom: 0.5rem;
  color: inherit;
}

.course-content :deep(blockquote p:last-child) {
  margin-bottom: 0;
}

/* ── Lists ────────────────────────────────────────────────────── */
.course-content :deep(ul) {
  list-style-type: disc;
  margin: 1.25rem 0;
  padding-left: 1.75rem;
}

.course-content :deep(ol) {
  list-style-type: decimal;
  margin: 1.25rem 0;
  padding-left: 1.75rem;
}

.course-content :deep(li) {
  margin-bottom: 0.5rem;
  padding-left: 0.25rem;
  line-height: 1.7;
  color: rgb(var(--color-body));
}

.course-content :deep(li::marker) {
  color: #60a5fa;
}

/* ── Images ───────────────────────────────────────────────────── */
.course-content :deep(img) {
  max-width: 100%;
  height: auto;
  margin: 1.75rem auto;
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: block;
}

/* ── Tables ───────────────────────────────────────────────────── */
.course-content :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1.75rem 0;
  font-size: 0.9375rem;
  overflow-x: auto;
  display: block;
}

.course-content :deep(thead) {
  background: #eff6ff;
}

.course-content :deep(th) {
  padding: 0.75rem 1rem;
  text-align: left;
  font-weight: 600;
  color: rgb(var(--color-heading));
  border-bottom: 2px solid #dbeafe;
}

.course-content :deep(td) {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgb(var(--color-border-muted));
  color: rgb(var(--color-body));
}

/* ── Horizontal Rule ──────────────────────────────────────────── */
.course-content :deep(hr) {
  margin: 2.5rem 0;
  border: none;
  height: 1px;
  background: linear-gradient(to right, transparent, rgb(var(--color-border)), transparent);
}

/* ── Responsive ───────────────────────────────────────────────── */
@media (max-width: 640px) {
  .course-content {
    font-size: 1rem;
  }

  .course-content :deep(h1) {
    font-size: 1.5rem;
    margin-top: 2rem;
  }

  .course-content :deep(h2) {
    font-size: 1.25rem;
    margin-top: 1.75rem;
  }

  .course-content :deep(h3) {
    font-size: 1.125rem;
    margin-top: 1.5rem;
  }

  .course-content :deep(img) {
    border-radius: 0.5rem;
    margin: 1.25rem auto;
  }
}
</style>
