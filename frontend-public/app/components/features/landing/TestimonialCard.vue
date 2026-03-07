<script setup lang="ts">
/**
 * TestimonialCard — Individual testimonial display card.
 *
 * Shows rating stars, testimonial content, author name and title.
 * Uses design system tokens exclusively.
 */
import type { Testimonial } from '~~/types';

defineProps<{
  testimonial: Testimonial
}>()
</script>

<template>
  <div class="group flex flex-col p-6 sm:p-7 rounded-2xl border border-border bg-surface shadow-soft hover:shadow-medium hover:-translate-y-1 transition-all duration-300">
    <!-- Author (top) -->
    <div class="flex items-center gap-3 mb-5">
      <UAvatar
        :src="testimonial.photo_url"
        :name="testimonial.name"
        size="lg"
      />
      <div class="min-w-0">
        <p class="text-sm font-semibold text-heading truncate">{{ testimonial.name }}</p>
        <p class="text-xs text-muted truncate">{{ testimonial.title }}</p>
      </div>
    </div>

    <!-- Stars -->
    <div class="flex items-center gap-0.5 mb-4">
      <template v-for="i in 5" :key="i">
        <svg
          v-if="i <= testimonial.rating"
          class="w-4 h-4 text-warning-400"
          fill="currentColor"
          viewBox="0 0 24 24"
        >
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
        </svg>
        <svg
          v-else
          class="w-4 h-4 text-border"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
        </svg>
      </template>
    </div>

    <!-- Content -->
    <p class="text-sm text-body leading-relaxed flex-1">
      "{{ testimonial.content }}"
    </p>
  </div>
</template>
