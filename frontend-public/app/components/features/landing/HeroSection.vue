<script setup lang="ts">
/**
 * HeroSection — Landing page hero banner.
 *
 * Typography-driven layout with headline, tagline, description, CTA,
 * and integrated platform statistics.
 * Site name and tagline come from the siteSettings store.
 */
import { useSiteSettingsStore } from '~~/stores/siteSettings';
import type { PlatformStatistics } from '~~/types';

interface Props {
  stats?: PlatformStatistics | null
}

withDefaults(defineProps<Props>(), {
  stats: null,
})

const siteSettings = useSiteSettingsStore()

const statItems = [
  { label: 'Pengguna', icon: 'lucide:users', key: 'total_users' as const },
  { label: 'Kursus', icon: 'lucide:book-open', key: 'total_courses' as const },
  { label: 'Webinar', icon: 'lucide:video', key: 'total_webinars' as const },
  { label: 'Artikel', icon: 'lucide:newspaper', key: 'total_articles' as const },
]

function formatNumber(value: number | undefined): string {
  if (!value) return '0'
  if (value >= 1000) {
    return `${(value / 1000).toFixed(value % 1000 === 0 ? 0 : 1)}k+`
  }
  return `${value}+`
}
</script>

<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-surface to-secondary-50">
    <div class="container py-20 sm:py-28 lg:py-32">
      <div class="max-w-3xl mx-auto text-center">
        <!-- Tagline badge -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-100/60 border border-primary-200/50 mb-6 animate-fade-in">
          <Icon name="lucide:sparkles" class="w-4 h-4 text-primary-500" />
          <span class="text-xs sm:text-sm font-medium text-primary-700">{{ siteSettings.siteTagline }}</span>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold text-heading leading-tight animate-fade-in-up">
          Wujudkan Pendidikan Anak Usia Dini yang
          <span class="text-primary-600"> Lebih Berkualitas</span>
        </h1>

        <!-- Description -->
        <p class="mt-5 sm:mt-6 text-base sm:text-lg lg:text-xl text-body leading-relaxed max-w-2xl mx-auto animate-fade-in-up">
          {{ siteSettings.siteDescription }}
        </p>

        <!-- CTA Buttons -->
        <div class="mt-8 sm:mt-10 flex flex-wrap gap-4 justify-center animate-fade-in-up">
          <NuxtLink to="/courses">
            <UButton variant="primary" size="lg" class="px-8">
              Jelajahi Platform
              <Icon name="lucide:arrow-right" class="w-4 h-4 ml-2" />
            </UButton>
          </NuxtLink>
          <NuxtLink to="/about">
            <UButton variant="outline" size="lg" class="px-8">
              Pelajari Selengkapnya
            </UButton>
          </NuxtLink>
        </div>
      </div>

      <!-- Stats row (integrated) -->
      <div class="mt-16 sm:mt-20 grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 max-w-4xl mx-auto animate-fade-in-up">
        <div
          v-for="item in statItems"
          :key="item.key"
          class="group text-center p-5 sm:p-6 rounded-2xl bg-surface/80 backdrop-blur-sm border border-border shadow-soft hover:shadow-card hover:-translate-y-0.5 transition-all duration-300"
        >
          <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-50 mb-3 group-hover:scale-110 transition-transform duration-300">
            <Icon :name="item.icon" class="w-5 h-5 text-primary-600" />
          </div>
          <p class="text-2xl sm:text-3xl font-bold text-heading tracking-tight">
            {{ stats ? formatNumber(stats[item.key]) : '—' }}
          </p>
          <p class="mt-1 text-xs sm:text-sm text-muted font-medium">{{ item.label }}</p>
        </div>
      </div>
    </div>

    <!-- Decorative elements -->
    <div class="absolute top-1/4 left-[5%] w-24 h-24 rounded-full bg-primary-200/20 blur-2xl" />
    <div class="absolute bottom-1/4 right-[5%] w-32 h-32 rounded-full bg-secondary-200/20 blur-2xl" />
    <div class="absolute top-[60%] left-[50%] w-16 h-16 rounded-full bg-primary-300/10 blur-xl" />
  </section>
</template>
