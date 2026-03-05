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
  <section class="bg-primary-600">
    <div class="container py-12 sm:py-16">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
        <div
          v-for="item in statItems"
          :key="item.key"
          class="text-center"
        >
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/10 mb-3">
            <Icon :name="item.icon" class="w-6 h-6 text-white" />
          </div>
          <p class="text-2xl sm:text-3xl font-bold text-white">
            {{ stats ? formatNumber(stats[item.key]) : '—' }}
          </p>
          <p class="mt-1 text-sm text-primary-100">{{ item.label }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
