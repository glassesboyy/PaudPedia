<script setup lang="ts">
/**
 * StatsSection — Platform statistics counter display.
 *
 * Data from the landing API. Uses design system tokens exclusively.
 */
import type { PlatformStatistics } from '~~/types';

interface Props {
  stats: PlatformStatistics | null
}

defineProps<Props>()

const statItems = computed(() => {
  return [
    { label: 'Pengguna', icon: 'lucide:users', key: 'total_users' },
    { label: 'Kursus', icon: 'lucide:book-open', key: 'total_courses' },
    { label: 'Webinar', icon: 'lucide:video', key: 'total_webinars' },
    { label: 'Artikel', icon: 'lucide:newspaper', key: 'total_articles' },
  ] as const
})

function formatNumber(value: number | undefined): string {
  if (!value) return '0'
  if (value >= 1000) {
    return `${(value / 1000).toFixed(value % 1000 === 0 ? 0 : 1)}k+`
  }
  return `${value}+`
}
</script>

<template>
  <section class="relative bg-gradient-to-r from-primary-600 via-primary-700 to-primary-800 overflow-hidden">
    <div class="container py-14 sm:py-20 relative z-10">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div
          v-for="item in statItems"
          :key="item.key"
          class="group relative text-center p-6 sm:p-8 rounded-2xl bg-white/[0.08] backdrop-blur-sm border border-white/[0.08] hover:bg-white/[0.14] transition-all duration-300"
        >
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-white/10 mb-4 group-hover:scale-110 transition-transform duration-300">
            <Icon :name="item.icon" class="w-6 h-6 text-primary-100" />
          </div>
          <p class="text-3xl sm:text-4xl font-bold text-white tracking-tight">
            {{ stats ? formatNumber(stats[item.key]) : '—' }}
          </p>
          <p class="mt-1.5 text-sm font-medium text-primary-200">{{ item.label }}</p>
        </div>
      </div>
    </div>

    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-40 h-40 rounded-full bg-white/[0.03] -translate-x-1/2 -translate-y-1/2" />
    <div class="absolute bottom-0 right-0 w-56 h-56 rounded-full bg-white/[0.03] translate-x-1/3 translate-y-1/3" />
  </section>
</template>
