<script setup lang="ts">
/**
 * My Courses Page (FR-UA-08)
 * Route: /account/courses
 *
 * Shows enrolled courses with progress bars, filters, and search.
 */
import { dashboardService } from '~~/services'
import type { UserCourse } from '~~/types'

definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Kursus Saya' })

const courses = ref<UserCourse[]>([])
const isLoading = ref(true)
const error = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)

// Filter
const activeFilter = ref<'all' | 'in-progress' | 'completed'>('all')

const filters = [
  { key: 'all' as const, label: 'Semua', icon: 'lucide:layers' },
  { key: 'in-progress' as const, label: 'Sedang Berjalan', icon: 'lucide:play-circle' },
  { key: 'completed' as const, label: 'Selesai', icon: 'lucide:check-circle' },
]

const filteredCourses = computed(() => {
  if (activeFilter.value === 'all') return courses.value
  if (activeFilter.value === 'completed') return courses.value.filter((c) => c.is_completed)
  return courses.value.filter((c) => !c.is_completed)
})

async function fetchCourses(page = 1) {
  isLoading.value = true
  error.value = ''
  try {
    const res = await dashboardService.getCourses({ page, per_page: 12 })
    courses.value = res.data
    currentPage.value = res.meta.current_page
    totalPages.value = res.meta.last_page
    totalItems.value = res.meta.total
  } catch {
    error.value = 'Gagal memuat data kursus.'
  } finally {
    isLoading.value = false
  }
}

function levelColor(level: string | null): string {
  const map: Record<string, string> = {
    beginner: 'bg-success-50 text-success-700 ring-1 ring-success-200',
    intermediate: 'bg-warning-50 text-warning-700 ring-1 ring-warning-200',
    advanced: 'bg-danger-50 text-danger-700 ring-1 ring-danger-200',
  }
  return map[level ?? ''] ?? 'bg-surface-muted text-muted'
}

function progressColor(pct: number): string {
  if (pct >= 100) return 'bg-success-500'
  if (pct >= 50) return 'bg-primary-500'
  return 'bg-warning-500'
}

onMounted(() => fetchCourses())
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold text-heading">Kursus Saya</h1>
        <span
          v-if="!isLoading && totalItems > 0"
          class="text-xs font-medium text-muted bg-surface-muted px-2.5 py-1 rounded-full"
        >
          {{ totalItems }} kursus
        </span>
      </div>
      <p class="text-sm text-muted">Pantau progres belajar dan lanjutkan kursus Anda.</p>
    </div>

    <!-- Filters -->
    <div v-if="!isLoading && courses.length > 0" class="flex flex-wrap gap-2 mb-5">
      <button
        v-for="f in filters"
        :key="f.key"
        type="button"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-200"
        :class="activeFilter === f.key
          ? 'bg-primary-500 text-white shadow-sm'
          : 'bg-surface-muted text-muted hover:bg-surface-sunken hover:text-body'"
        @click="activeFilter = f.key"
      >
        <Icon :name="f.icon" class="w-3.5 h-3.5" />
        {{ f.label }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-for="n in 6" :key="n" class="rounded-2xl border border-border bg-surface overflow-hidden">
        <USkeleton variant="rectangular" class="aspect-video w-full" />
        <div class="p-4 space-y-3">
          <USkeleton variant="text" class="w-3/4 h-4" />
          <USkeleton variant="text" class="w-1/2 h-3" />
          <USkeleton variant="rounded" class="w-full h-2" />
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
      <UButton variant="primary" size="sm" @click="fetchCourses(currentPage)">
        <Icon name="lucide:refresh-cw" class="w-3.5 h-3.5 mr-1.5" />
        Coba Lagi
      </UButton>
    </div>

    <!-- Empty -->
    <div v-else-if="!courses.length" class="relative overflow-hidden rounded-2xl border border-border bg-surface p-10 text-center">
      <div class="absolute inset-0 bg-gradient-to-br from-primary-50/40 via-transparent to-secondary-50/30 pointer-events-none" />
      <div class="relative">
        <div class="w-20 h-20 rounded-3xl bg-primary-50 flex items-center justify-center mx-auto mb-5 shadow-sm">
          <Icon name="lucide:book-open" class="w-10 h-10 text-primary-400" />
        </div>
        <h3 class="text-lg font-bold text-heading mb-2">Mulai Perjalanan Belajar Anda</h3>
        <p class="text-sm text-muted mb-6 max-w-sm mx-auto">
          Temukan kursus menarik untuk meningkatkan keterampilan Anda di bidang pendidikan anak usia dini.
        </p>
        <NuxtLink to="/courses">
          <UButton variant="primary" size="md">
            <Icon name="lucide:compass" class="w-4 h-4 mr-1.5" />
            Jelajahi Kursus
          </UButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Filter Empty -->
    <div
      v-else-if="filteredCourses.length === 0"
      class="rounded-2xl border border-border bg-surface-muted/30 p-10 text-center"
    >
      <Icon name="lucide:search-x" class="w-10 h-10 text-muted mx-auto mb-3" />
      <p class="text-sm text-muted">Tidak ada kursus dengan filter ini.</p>
    </div>

    <!-- Course Grid -->
    <template v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        <NuxtLink
          v-for="item in filteredCourses"
          :key="item.id"
          :to="item.first_lesson_id ? `/learn/${item.slug}/${item.first_lesson_id}` : `/courses/${item.slug}`"
          class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-medium hover:border-primary-200 hover:-translate-y-0.5 transition-all duration-300"
        >
          <!-- Thumbnail -->
          <div class="relative aspect-video overflow-hidden bg-surface-sunken">
            <img
              v-if="item.thumbnail_url"
              :src="item.thumbnail_url"
              :alt="item.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100/50">
              <Icon name="lucide:book-open" class="w-10 h-10 text-primary-300" />
            </div>

            <!-- Level badge -->
            <span
              v-if="item.level"
              :class="['absolute top-3 left-3 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider ring-inset', levelColor(item.level)]"
            >
              {{ item.level_label ?? item.level }}
            </span>

            <!-- Completed badge -->
            <span
              v-if="item.is_completed"
              class="absolute top-3 right-3 px-2 py-0.5 rounded-md text-[10px] font-bold bg-success-500 text-white shadow-sm flex items-center gap-1"
            >
              <Icon name="lucide:check" class="w-3 h-3" />
              Selesai
            </span>
          </div>

          <!-- Content -->
          <div class="flex-1 flex flex-col p-4">
            <h3 class="text-sm font-semibold text-heading group-hover:text-primary-600 line-clamp-2 mb-2 leading-snug transition-colors">
              {{ item.title }}
            </h3>

            <!-- Mentor -->
            <div v-if="item.mentor" class="flex items-center gap-2 mb-3">
              <div class="w-5 h-5 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img
                  v-if="item.mentor.photo_url"
                  :src="item.mentor.photo_url"
                  :alt="item.mentor.name"
                  class="w-full h-full object-cover"
                />
                <span v-else class="text-[9px] font-bold text-primary-600">
                  {{ item.mentor.name?.charAt(0)?.toUpperCase() }}
                </span>
              </div>
              <span class="text-xs text-muted truncate">{{ item.mentor.name }}</span>
            </div>

            <!-- Progress -->
            <div class="mt-auto pt-3 border-t border-border-muted">
              <div class="flex items-center justify-between text-xs mb-1.5">
                <span class="text-muted">
                  {{ item.completed_lessons }}/{{ item.total_lessons }} pelajaran
                </span>
                <span class="font-semibold" :class="item.progress_percentage >= 100 ? 'text-success-600' : 'text-primary-600'">
                  {{ item.progress_percentage }}%
                </span>
              </div>
              <div class="w-full h-1.5 rounded-full bg-surface-sunken overflow-hidden">
                <div
                  :class="['h-full rounded-full transition-all duration-500', progressColor(item.progress_percentage)]"
                  :style="{ width: `${Math.min(item.progress_percentage, 100)}%` }"
                />
              </div>
            </div>
          </div>
        </NuxtLink>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-center gap-1.5 mt-8">
        <UButton
          variant="outline"
          size="sm"
          :disabled="currentPage <= 1"
          @click="fetchCourses(currentPage - 1)"
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
            @click="fetchCourses(p)"
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
          @click="fetchCourses(currentPage + 1)"
        >
          <Icon name="lucide:chevron-right" class="w-4 h-4" />
        </UButton>
      </div>
    </template>
  </div>
</template>
