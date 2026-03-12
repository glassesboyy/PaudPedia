<script setup lang="ts">
/**
 * My Webinars Page (FR-UA-10)
 * Route: /account/webinars
 *
 * Shows registered webinars with status filters and zoom links.
 */
import { dashboardService } from '~~/services'
import type { UserWebinar } from '~~/types'

definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Webinar Saya' })

const webinars = ref<UserWebinar[]>([])
const isLoading = ref(true)
const error = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)

// Filter
const activeFilter = ref<'all' | 'upcoming' | 'finished'>('all')

const filters = [
  { key: 'all' as const, label: 'Semua', icon: 'lucide:layers' },
  { key: 'upcoming' as const, label: 'Akan Datang', icon: 'lucide:calendar-clock' },
  { key: 'finished' as const, label: 'Selesai', icon: 'lucide:check-circle' },
]

const filteredWebinars = computed(() => {
  if (activeFilter.value === 'all') return webinars.value
  return webinars.value.filter((w) => w.status === activeFilter.value)
})

async function fetchWebinars(page = 1) {
  isLoading.value = true
  error.value = ''
  try {
    const res = await dashboardService.getWebinars({ page, per_page: 12 })
    webinars.value = res.data
    currentPage.value = res.meta.current_page
    totalPages.value = res.meta.last_page
    totalItems.value = res.meta.total
  } catch {
    error.value = 'Gagal memuat data webinar.'
  } finally {
    isLoading.value = false
  }
}

function formatDuration(minutes: number | null): string {
  if (!minutes) return ''
  if (minutes < 60) return `${minutes} mnt`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m > 0 ? `${h}j ${m}m` : `${h} jam`
}

onMounted(() => fetchWebinars())
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold text-heading">Webinar Saya</h1>
        <span
          v-if="!isLoading && totalItems > 0"
          class="text-xs font-medium text-muted bg-surface-muted px-2.5 py-1 rounded-full"
        >
          {{ totalItems }} webinar
        </span>
      </div>
      <p class="text-sm text-muted">Kelola jadwal webinar dan akses link meeting Anda.</p>
    </div>

    <!-- Filters -->
    <div v-if="!isLoading && webinars.length > 0" class="flex flex-wrap gap-2 mb-5">
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
      <UButton variant="primary" size="sm" @click="fetchWebinars(currentPage)">
        <Icon name="lucide:refresh-cw" class="w-3.5 h-3.5 mr-1.5" />
        Coba Lagi
      </UButton>
    </div>

    <!-- Empty -->
    <div v-else-if="!webinars.length" class="relative overflow-hidden rounded-2xl border border-border bg-surface p-10 text-center">
      <div class="absolute inset-0 bg-gradient-to-br from-secondary-50/40 via-transparent to-primary-50/30 pointer-events-none" />
      <div class="relative">
        <div class="w-20 h-20 rounded-3xl bg-secondary-50 flex items-center justify-center mx-auto mb-5 shadow-sm">
          <Icon name="lucide:video" class="w-10 h-10 text-secondary-400" />
        </div>
        <h3 class="text-lg font-bold text-heading mb-2">Belum Ada Webinar</h3>
        <p class="text-sm text-muted mb-6 max-w-sm mx-auto">
          Ikuti webinar interaktif bersama para ahli di bidang pendidikan anak usia dini.
        </p>
        <NuxtLink to="/webinars">
          <UButton variant="primary" size="md">
            <Icon name="lucide:calendar-plus" class="w-4 h-4 mr-1.5" />
            Jelajahi Webinar
          </UButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Filter Empty -->
    <div
      v-else-if="filteredWebinars.length === 0"
      class="rounded-2xl border border-border bg-surface-muted/30 p-10 text-center"
    >
      <Icon name="lucide:search-x" class="w-10 h-10 text-muted mx-auto mb-3" />
      <p class="text-sm text-muted">Tidak ada webinar dengan filter ini.</p>
    </div>

    <!-- Webinar Grid -->
    <template v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        <div
          v-for="item in filteredWebinars"
          :key="item.id"
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
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-secondary-50 to-secondary-100/50">
              <Icon name="lucide:video" class="w-10 h-10 text-secondary-300" />
            </div>

            <!-- Status badge -->
            <span
              class="absolute top-3 left-3 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider"
              :class="item.status === 'upcoming'
                ? 'bg-success-500 text-white'
                : 'bg-muted/70 text-white backdrop-blur-sm'"
            >
              <span class="flex items-center gap-1">
                <span v-if="item.status === 'upcoming'" class="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                {{ item.status_label }}
              </span>
            </span>

            <!-- Duration badge -->
            <span
              v-if="item.duration_minutes"
              class="absolute top-3 right-3 px-2 py-0.5 rounded-md text-[10px] font-bold bg-black/50 text-white backdrop-blur-sm"
            >
              {{ formatDuration(item.duration_minutes) }}
            </span>
          </div>

          <!-- Content -->
          <div class="flex-1 flex flex-col p-4">
            <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 leading-snug">
              {{ item.title }}
            </h3>

            <!-- Mentor -->
            <div v-if="item.mentor" class="flex items-center gap-2 mb-3">
              <div class="w-5 h-5 rounded-full bg-secondary-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img
                  v-if="item.mentor.photo_url"
                  :src="item.mentor.photo_url"
                  :alt="item.mentor.name"
                  class="w-full h-full object-cover"
                />
                <span v-else class="text-[9px] font-bold text-secondary-600">
                  {{ item.mentor.name?.charAt(0)?.toUpperCase() }}
                </span>
              </div>
              <span class="text-xs text-muted truncate">{{ item.mentor.name }}</span>
            </div>

            <!-- Schedule & CTA -->
            <div class="mt-auto pt-3 border-t border-border-muted space-y-2.5">
              <div v-if="item.scheduled_date" class="flex items-center gap-1.5 text-xs text-muted mb-4">
                <Icon name="lucide:calendar" class="w-3.5 h-3.5 flex-shrink-0" />
                <span>{{ item.scheduled_date }}</span>
                <template v-if="item.scheduled_time">
                  <span class="text-border">|</span>
                  <Icon name="lucide:clock" class="w-3.5 h-3.5 flex-shrink-0" />
                  <span>{{ item.scheduled_time }}</span>
                </template>
              </div>

              <!-- Join / Detail button -->
              <a
                v-if="item.status === 'upcoming' && item.zoom_link"
                :href="item.zoom_link"
                target="_blank"
                rel="noopener noreferrer"
              >
                <UButton variant="primary" size="sm" block>
                  <Icon name="lucide:video" class="w-3.5 h-3.5 mr-1.5" />
                  Join Webinar
                </UButton>
              </a>
              <NuxtLink v-else-if="item.slug" :to="`/webinars/${item.slug}`">
                <UButton variant="outline" size="sm" block>
                  <Icon name="lucide:eye" class="w-3.5 h-3.5 mr-1.5" />
                  Lihat Detail
                </UButton>
              </NuxtLink>
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
          @click="fetchWebinars(currentPage - 1)"
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
            @click="fetchWebinars(p)"
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
          @click="fetchWebinars(currentPage + 1)"
        >
          <Icon name="lucide:chevron-right" class="w-4 h-4" />
        </UButton>
      </div>
    </template>
  </div>
</template>
