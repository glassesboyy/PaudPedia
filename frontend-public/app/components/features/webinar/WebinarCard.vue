<script setup lang="ts">
/**
 * WebinarCard — Reusable webinar preview card.
 *
 * Displays thumbnail, title, mentor, scheduled date, price.
 * Uses design system tokens exclusively.
 */
import type { Webinar } from '~~/types';

defineProps<{
  webinar: Webinar
}>()

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
      month: 'short',
      year: 'numeric',
    })
  } catch {
    return dateStr
  }
}
</script>

<template>
  <NuxtLink
    :to="`/webinars/${webinar.slug}`"
    class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-card transition-all duration-300"
  >
    <!-- Thumbnail -->
    <div class="relative aspect-video overflow-hidden bg-surface-sunken">
      <img
        v-if="webinar.thumbnail_url"
        :src="webinar.thumbnail_url"
        :alt="webinar.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <Icon name="lucide:video" class="w-10 h-10 text-muted" />
      </div>
      <!-- Live badge -->
      <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-medium bg-secondary-50 text-secondary-700">
        Webinar
      </span>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-4">
      <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors">
        {{ webinar.title }}
      </h3>

      <!-- Mentor -->
      <div v-if="webinar.mentor" class="flex items-center gap-2 mb-3">
        <UAvatar :name="webinar.mentor.name" size="sm" />
        <span class="text-xs text-body truncate">{{ webinar.mentor.name }}</span>
      </div>

      <div class="mt-auto pt-3 border-t border-border-muted flex items-center justify-between">
        <!-- Schedule -->
        <div class="flex items-center gap-1.5 text-xs text-muted">
          <Icon name="lucide:calendar" class="w-3.5 h-3.5" />
          <span>{{ (webinar as any).scheduled_date || formatDate((webinar as any).scheduled_at || webinar.start_date) }}</span>
        </div>

        <!-- Price -->
        <span class="text-sm font-bold text-primary-600">
          {{ webinar.price === 0 ? 'Gratis' : formatPrice(webinar.price) }}
        </span>
      </div>
    </div>
  </NuxtLink>
</template>
