<script setup lang="ts">
/**
 * WebinarCard — Reusable webinar preview card.
 *
 * Displays thumbnail, title, mentor, date, time, duration, price, discount.
 * Uses design system tokens exclusively.
 */
import type { LandingWebinar, Webinar } from '~~/types';

defineProps<{
  webinar: Webinar | LandingWebinar
}>()

function formatPrice(price: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}

function formatDuration(minutes: number | null): string {
  if (!minutes) return ''
  if (minutes < 60) return `${minutes} menit`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m > 0 ? `${h} jam ${m} menit` : `${h} jam`
}
</script>

<template>
  <NuxtLink
    :to="`/webinars/${webinar.slug}`"
    class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-medium hover:border-secondary-200 hover:-translate-y-1 transition-all duration-300"
  >
    <!-- Thumbnail -->
    <div class="relative aspect-video overflow-hidden bg-surface-muted">
      <img
        v-if="webinar.thumbnail_url"
        :src="webinar.thumbnail_url"
        :alt="webinar.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        loading="lazy"
      />
      <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-secondary-50 to-secondary-100/50">
        <Icon name="lucide:video" class="w-10 h-10 text-secondary-300" />
      </div>

      <!-- Status badge -->
      <span
        class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[11px] font-semibold shadow-sm"
        :class="webinar.is_upcoming
          ? 'bg-success-500 text-white'
          : 'bg-muted/70 text-white backdrop-blur-sm'"
      >
        <span class="flex items-center gap-1">
          <span v-if="webinar.is_upcoming" class="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
          {{ webinar.is_upcoming ? 'Akan Datang' : 'Selesai' }}
        </span>
      </span>

      <!-- Discount badge -->
      <span
        v-if="webinar.has_discount && webinar.discount_percentage"
        class="absolute top-3 right-3 px-2 py-0.5 rounded-full text-[10px] font-bold bg-danger-500 text-white shadow-sm"
      >
        -{{ webinar.discount_percentage }}%
      </span>

      <!-- Owned Badge -->
      <span
        v-if="webinar.is_owned"
        class="absolute bottom-3 left-3 px-2 py-0.5 rounded-lg text-[11px] font-bold bg-success-500 text-white shadow-sm flex items-center gap-1"
      >
        <Icon name="lucide:check-circle" class="w-3 h-3" />
        Terdaftar
      </span>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-4 gap-2">
      <!-- Title -->
      <h3 class="text-sm font-semibold text-heading line-clamp-2 group-hover:text-secondary-600 transition-colors leading-snug">
        {{ webinar.title }}
      </h3>

      <!-- Description excerpt -->
      <p v-if="webinar.description" class="text-xs text-muted line-clamp-2 leading-relaxed">
        {{ webinar.description }}
      </p>

      <!-- Mentor -->
      <div v-if="webinar.mentor" class="flex items-center gap-2 mt-1">
        <img
          v-if="webinar.mentor.photo_url"
          :src="webinar.mentor.photo_url"
          :alt="webinar.mentor.name"
          class="w-6 h-6 rounded-full object-cover border border-border"
        />
        <div v-else class="w-6 h-6 rounded-full bg-secondary-50 flex items-center justify-center border border-border">
          <Icon name="lucide:user" class="w-3 h-3 text-secondary-400" />
        </div>
        <span class="text-xs text-body truncate">{{ webinar.mentor.name }}</span>
      </div>

      <!-- Meta row: date + duration -->
      <div class="mt-auto pt-3 border-t border-border/50 space-y-1.5">
        <div class="flex items-center justify-between text-xs text-muted">
          <span v-if="webinar.scheduled_date" class="flex items-center gap-1.5">
            <Icon name="lucide:calendar" class="w-3.5 h-3.5" />
            {{ webinar.scheduled_date }}
            <template v-if="webinar.scheduled_time">· {{ webinar.scheduled_time }}</template>
          </span>
          <span v-if="webinar.duration_minutes" class="flex items-center gap-1">
            <Icon name="lucide:clock" class="w-3.5 h-3.5" />
            {{ formatDuration(webinar.duration_minutes) }}
          </span>
        </div>

        <!-- Price row -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span
              class="text-sm font-bold"
              :class="webinar.price === 0 ? 'text-success-600' : 'text-secondary-600'"
            >
              {{ webinar.price === 0 ? 'Gratis' : formatPrice(webinar.price) }}
            </span>
            <span
              v-if="webinar.has_discount && webinar.original_price"
              class="text-[11px] text-muted line-through"
            >
              {{ formatPrice(webinar.original_price) }}
            </span>
          </div>
          <span v-if="webinar.max_participants" class="text-[10px] text-muted flex items-center gap-1">
            <Icon name="lucide:users" class="w-3 h-3" />
            {{ webinar.max_participants }}
          </span>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>
