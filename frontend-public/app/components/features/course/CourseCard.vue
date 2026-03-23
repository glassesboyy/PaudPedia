<script setup lang="ts">
/**
 * CourseCard — Reusable course preview card.
 *
 * Displays thumbnail, title, mentor, price, level, duration, and discount info.
 * Uses design system tokens exclusively.
 */
import type { Course, LandingCourse } from '~~/types';

defineProps<{
  course: Course | LandingCourse
}>()

const levelLabels: Record<string, string> = {
  beginner: 'Pemula',
  intermediate: 'Menengah',
  advanced: 'Lanjutan',
}

const levelColors: Record<string, string> = {
  beginner: 'bg-success-50 text-success-700',
  intermediate: 'bg-warning-50 text-warning-700',
  advanced: 'bg-danger-50 text-danger-700',
}

function formatPrice(price: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}
</script>

<template>
  <NuxtLink
    :to="`/courses/${course.slug}`"
    class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-medium hover:border-primary-200 hover:-translate-y-1 transition-all duration-300"
  >
    <!-- Thumbnail -->
    <div class="relative aspect-video overflow-hidden bg-surface-sunken">
      <img
        v-if="course.thumbnail_url"
        :src="course.thumbnail_url"
        :alt="course.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />
      <div v-else class="w-full h-full flex items-center justify-center bg-primary-50/50">
        <Icon name="lucide:book-open" class="w-12 h-12 text-primary-300" />
      </div>

      <!-- Level badge -->
      <span
        v-if="course.level"
        :class="['absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide shadow-sm', levelColors[course.level] || 'bg-surface text-body']"
      >
        {{ course.level_label || levelLabels[course.level] || course.level }}
      </span>

      <!-- Discount badge -->
      <span
        v-if="course.has_discount && course.discount_percentage"
        class="absolute top-3 right-3 px-2 py-0.5 rounded-lg text-[11px] font-bold bg-danger-500 text-white shadow-sm"
      >
        -{{ course.discount_percentage }}%
      </span>

      <!-- Owned Badge -->
      <span
        v-if="course.is_owned"
        class="absolute bottom-3 left-3 px-2 py-0.5 rounded-lg text-[11px] font-bold bg-success-500 text-white shadow-sm flex items-center gap-1"
      >
        <Icon name="lucide:check-circle" class="w-3 h-3" />
        Dimiliki
      </span>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-4">
      <!-- Category -->
      <p v-if="course.category" class="text-[11px] text-primary-600 font-semibold uppercase tracking-wide mb-1.5">
        {{ course.category.name }}
      </p>

      <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors leading-snug">
        {{ course.title }}
      </h3>

      <!-- Description excerpt -->
      <p v-if="course.description" class="text-xs text-body/70 line-clamp-2 mb-2 leading-relaxed">
        {{ course.description }}
      </p>

      <!-- Mentor -->
      <div v-if="course.mentor" class="flex items-center gap-2 mb-3">
        <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
          <img
            v-if="course.mentor.photo_url"
            :src="course.mentor.photo_url"
            :alt="course.mentor.name"
            class="w-full h-full object-cover"
            loading="lazy"
          />
          <span v-else class="text-[10px] font-bold text-primary-600">
            {{ course.mentor.name?.charAt(0)?.toUpperCase() }}
          </span>
        </div>
        <span class="text-xs text-body truncate">{{ course.mentor.name }}</span>
      </div>

      <!-- Footer -->
      <div class="mt-auto pt-3 border-t border-border-muted flex items-center justify-between gap-2">
        <!-- Meta -->
        <div class="flex items-center gap-3 text-xs text-muted min-w-0">
          <span v-if="course.duration_hours" class="inline-flex items-center gap-1 shrink-0">
            <Icon name="lucide:clock" class="w-3.5 h-3.5" />
            {{ course.duration_hours }}j
          </span>
          <span v-if="course.modules_count" class="inline-flex items-center gap-1 shrink-0">
            <Icon name="lucide:layers" class="w-3.5 h-3.5" />
            {{ course.modules_count }} modul
          </span>
        </div>

        <!-- Price -->
        <div class="text-right shrink-0">
          <span
            v-if="course.has_discount && course.original_price"
            class="block text-[11px] text-muted line-through"
          >
            {{ formatPrice(course.original_price) }}
          </span>
          <span class="text-sm font-bold" :class="course.price === 0 ? 'text-success-600' : 'text-primary-600'">
            {{ course.price === 0 ? 'Gratis' : formatPrice(course.price) }}
          </span>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>
