<script setup lang="ts">
/**
 * Homepage / Landing Page
 * Route: /
 *
 * Fetches all landing data from /api/v1/landing in a single request.
 * Each section receives its data via props.
 */
import { landingService } from '~~/services'
import type { LandingPageData } from '~~/types'

useSeo({
  title: 'Beranda',
  description: 'Platform e-learning dan marketplace untuk pendidikan anak usia dini (PAUD)',
})

const landingData = ref<LandingPageData | null>(null)
const isLoading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const response = await landingService.getData()
    landingData.value = response.data
  } catch {
    error.value = 'Gagal memuat data. Silakan muat ulang halaman.'
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <div>
    <!-- Loading state -->
    <div v-if="isLoading" class="min-h-[60vh] flex items-center justify-center">
      <div class="text-center">
        <svg class="animate-spin h-8 w-8 text-primary-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
        <p class="mt-3 text-sm text-muted">Memuat halaman...</p>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="container py-16 text-center">
      <Icon name="lucide:alert-circle" class="w-12 h-12 text-danger-400 mx-auto" />
      <p class="mt-4 text-body">{{ error }}</p>
      <UButton variant="outline" size="sm" class="mt-4" @click="$router.go(0)">
        Muat Ulang
      </UButton>
    </div>

    <!-- Content -->
    <template v-else>
      <!-- FR-PP-01: Hero Section -->
      <HeroSection
        :hero="landingData?.settings.hero ?? null"
        :site-name="landingData?.settings.site_name"
      />

      <!-- FR-PP-01: Features Section (static) -->
      <FeaturesSection />

      <!-- FR-PP-01: Statistics Section -->
      <StatsSection :stats="landingData?.statistics ?? null" />

      <!-- FR-PP-02: Featured Courses -->
      <FeaturedCoursesSection
        v-if="landingData?.featured_courses?.length"
        :courses="landingData.featured_courses"
      />

      <!-- FR-PP-02: Featured Webinars -->
      <FeaturedWebinarsSection
        v-if="landingData?.featured_webinars?.length"
        :webinars="landingData.featured_webinars"
      />

      <!-- FR-PP-02: Featured Products -->
      <FeaturedProductsSection
        v-if="landingData?.featured_products?.length"
        :products="landingData.featured_products"
      />

      <!-- FR-PP-03: Testimonials -->
      <TestimonialsSection
        v-if="landingData?.testimonials?.length"
        :testimonials="landingData.testimonials"
      />

      <!-- CTA Section -->
      <CtaSection />
    </template>
  </div>
</template>
