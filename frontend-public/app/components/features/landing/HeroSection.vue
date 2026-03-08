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
      <div class="mt-14 sm:mt-16 max-w-4xl mx-auto animate-fade-in-up">
        <!-- Divider -->
        <div class="flex items-center gap-4 mb-10">
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-border to-transparent" />
          <span class="text-xs text-muted/60 uppercase tracking-widest font-medium">Statistik Platform</span>
          <div class="flex-1 h-px bg-gradient-to-r from-transparent via-border to-transparent" />
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4">
          <div
            v-for="(item, index) in statItems"
            :key="item.key"
            class="group flex items-center justify-center gap-3 py-4 sm:py-0"
            :class="[
              index !== 0 ? 'border-l border-border/40' : '',
              index < 2 ? 'border-b sm:border-b-0 border-border/40' : '',
            ]"
          >
            <div class="w-9 h-9 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-100 group-hover:scale-110 transition-all duration-200">
              <Icon :name="item.icon" class="w-4.5 h-4.5 text-primary-600" />
            </div>
            <div>
              <p class="text-xl sm:text-2xl font-bold text-heading tracking-tight leading-none">
                {{ stats ? formatNumber(stats[item.key]) : '—' }}
              </p>
              <p class="text-xs text-muted font-medium mt-0.5">{{ item.label }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Decorative elements -->
    <div class="absolute top-1/4 left-[5%] w-24 h-24 rounded-full bg-primary-200/20 blur-2xl" />
    <div class="absolute bottom-1/4 right-[5%] w-32 h-32 rounded-full bg-secondary-200/20 blur-2xl" />
    <div class="absolute top-[60%] left-[50%] w-16 h-16 rounded-full bg-primary-300/10 blur-xl" />
  </section>
</template>
