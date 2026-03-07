<script setup lang="ts">
/**
 * CourseCard — Reusable course preview card.
 *
 * Displays thumbnail, title, mentor, price, level, and duration.
 * Uses design system tokens exclusively.
 */
import type { Course } from '~~/types';

defineProps<{
  course: Course
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
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <Icon name="lucide:book-open" class="w-10 h-10 text-muted" />
      </div>
      <!-- Level badge -->
      <span
        v-if="course.level"
        :class="['absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-medium', levelColors[course.level] || 'bg-surface text-body']"
      >
        {{ levelLabels[course.level] || course.level }}
      </span>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-4">
      <!-- Category -->
      <p v-if="course.category" class="text-xs text-primary-600 font-medium mb-1.5">
        {{ course.category.name }}
      </p>

      <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors">
        {{ course.title }}
      </h3>

      <!-- Mentor -->
      <div v-if="course.mentor" class="flex items-center gap-2 mb-3">
        <UAvatar :name="course.mentor.name" size="sm" />
        <span class="text-xs text-body truncate">{{ course.mentor.name }}</span>
      </div>

      <div class="mt-auto pt-3 border-t border-border-muted flex items-center justify-between">
        <!-- Meta -->
        <div class="flex items-center gap-3 text-xs text-muted">
          <span v-if="course.duration_hours" class="flex items-center gap-1">
            <Icon name="lucide:clock" class="w-3.5 h-3.5" />
            {{ course.duration_hours }}j
          </span>
          <span v-if="course.modules_count" class="flex items-center gap-1">
            <Icon name="lucide:layers" class="w-3.5 h-3.5" />
            {{ course.modules_count }} modul
          </span>
        </div>

        <!-- Price -->
        <div class="text-right">
          <span
            v-if="course.original_price && course.original_price > course.price"
            class="block text-xs text-muted line-through"
          >
            {{ formatPrice(course.original_price) }}
          </span>
          <span class="text-sm font-bold text-primary-600">
            {{ course.price === 0 ? 'Gratis' : formatPrice(course.price) }}
          </span>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>
