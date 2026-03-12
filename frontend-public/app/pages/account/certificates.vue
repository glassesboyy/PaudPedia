<script setup lang="ts">
/**
 * My Certificates Page (FR-UA-11)
 * Route: /account/certificates
 *
 * Lists completed course certificates with download links.
 */
import { dashboardService } from '~~/services'
import type { UserCertificate } from '~~/types'

definePageMeta({
  layout: 'dashboard',
  middleware: ['auth', 'email-verified'],
})

useSeo({ title: 'Sertifikat Saya' })

const certificates = ref<UserCertificate[]>([])
const isLoading = ref(true)
const error = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)

async function fetchCertificates(page = 1) {
  isLoading.value = true
  error.value = ''
  try {
    const res = await dashboardService.getCertificates({ page, per_page: 12 })
    certificates.value = res.data
    currentPage.value = res.meta.current_page
    totalPages.value = res.meta.last_page
    totalItems.value = res.meta.total
  } catch {
    error.value = 'Gagal memuat data sertifikat.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => fetchCertificates())
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold text-heading">Sertifikat Saya</h1>
        <span
          v-if="!isLoading && totalItems > 0"
          class="text-xs font-medium text-muted bg-surface-muted px-2.5 py-1 rounded-full"
        >
          {{ totalItems }} sertifikat
        </span>
      </div>
      <p class="text-sm text-muted">Lihat dan unduh sertifikat dari kursus yang telah Anda selesaikan.</p>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-for="n in 6" :key="n" class="rounded-2xl border border-border bg-surface overflow-hidden">
        <USkeleton variant="rectangular" class="h-28 w-full" />
        <div class="p-4 space-y-3">
          <USkeleton variant="text" class="w-3/4 h-4" />
          <USkeleton variant="text" class="w-1/2 h-3" />
          <div class="flex gap-2 pt-2">
            <USkeleton variant="rectangular" class="flex-1 h-9 rounded-lg" />
            <USkeleton variant="rectangular" class="flex-1 h-9 rounded-lg" />
          </div>
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
      <UButton variant="primary" size="sm" @click="fetchCertificates(currentPage)">
        <Icon name="lucide:refresh-cw" class="w-3.5 h-3.5 mr-1.5" />
        Coba Lagi
      </UButton>
    </div>

    <!-- Empty -->
    <div v-else-if="!certificates.length" class="relative overflow-hidden rounded-2xl border border-border bg-surface p-10 text-center">
      <div class="absolute inset-0 bg-gradient-to-br from-warning-50/40 via-transparent to-secondary-50/30 pointer-events-none" />
      <div class="relative">
        <div class="w-20 h-20 rounded-3xl bg-warning-50 flex items-center justify-center mx-auto mb-5 shadow-sm">
          <Icon name="lucide:award" class="w-10 h-10 text-warning-400" />
        </div>
        <h3 class="text-lg font-bold text-heading mb-2">Belum Ada Sertifikat</h3>
        <p class="text-sm text-muted mb-6 max-w-sm mx-auto">
          Selesaikan kursus untuk mendapatkan sertifikat. Mulai belajar sekarang!
        </p>
        <NuxtLink to="/courses">
          <UButton variant="primary" size="md">
            <Icon name="lucide:graduation-cap" class="w-4 h-4 mr-1.5" />
            Jelajahi Kursus
          </UButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Certificate Grid -->
    <template v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        <div
          v-for="cert in certificates"
          :key="cert.id"
          class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-medium hover:border-warning-200 hover:-translate-y-0.5 transition-all duration-300"
        >
          <!-- Certificate visual header -->
          <div class="bg-gradient-to-br from-warning-50 via-warning-100/40 to-secondary-50/30 p-5 relative overflow-hidden">
            <div class="flex items-start justify-between relative z-10">
              <div class="w-12 h-12 rounded-xl bg-white/80 flex items-center justify-center shadow-sm backdrop-blur-sm">
                <Icon name="lucide:award" class="w-6 h-6 text-warning-500" />
              </div>
              <span class="text-[10px] font-bold text-warning-700 bg-warning-200/60 px-2 py-0.5 rounded-md uppercase tracking-wider">
                Sertifikat
              </span>
            </div>
            <!-- Decorative -->
            <div class="absolute -bottom-4 -right-4 w-28 h-28 bg-white/10 rounded-full" />
            <div class="absolute -bottom-8 -right-8 w-20 h-20 bg-white/5 rounded-full" />
          </div>

          <!-- Content -->
          <div class="flex-1 flex flex-col p-4">
            <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 leading-snug">
              {{ cert.course_title }}
            </h3>

            <p v-if="cert.issue_date_formatted" class="text-xs text-muted flex items-center gap-1.5 mb-4">
              <Icon name="lucide:calendar-check" class="w-3.5 h-3.5" />
              Diterbitkan {{ cert.issue_date_formatted }}
            </p>

            <!-- Actions -->
            <div class="mt-auto pt-3 border-t border-border-muted flex gap-2">
              <a
                v-if="cert.certificate_url"
                :href="cert.certificate_url"
                target="_blank"
                rel="noopener noreferrer"
                class="flex-1"
              >
                <UButton variant="outline" size="sm" block>
                  <Icon name="lucide:eye" class="w-3.5 h-3.5 mr-1.5" />
                  Lihat
                </UButton>
              </a>
              <a
                v-if="cert.download_url"
                :href="cert.download_url"
                download
                class="flex-1"
              >
                <UButton variant="primary" size="sm" block>
                  <Icon name="lucide:download" class="w-3.5 h-3.5 mr-1.5" />
                  Unduh
                </UButton>
              </a>
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
          @click="fetchCertificates(currentPage - 1)"
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
            @click="fetchCertificates(p)"
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
          @click="fetchCertificates(currentPage + 1)"
        >
          <Icon name="lucide:chevron-right" class="w-4 h-4" />
        </UButton>
      </div>
    </template>
  </div>
</template>
