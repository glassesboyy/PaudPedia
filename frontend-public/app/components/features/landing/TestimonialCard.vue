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
  <div class="flex flex-col p-6 rounded-2xl border border-border bg-surface shadow-soft">
    <!-- Stars -->
    <div class="flex items-center gap-0.5 mb-3">
      <Icon
        v-for="i in 5"
        :key="i"
        name="lucide:star"
        :class="[
          'w-4 h-4',
          i <= testimonial.rating ? 'text-warning-400 fill-warning-400' : 'text-border',
        ]"
      />
    </div>

    <!-- Content -->
    <p class="text-sm text-body leading-relaxed flex-1 mb-4">
      "{{ testimonial.content }}"
    </p>

    <!-- Author -->
    <div class="flex items-center gap-3 pt-4 border-t border-border-muted">
      <UAvatar
        :src="testimonial.photo_url"
        :name="testimonial.name"
        size="md"
      />
      <div class="min-w-0">
        <p class="text-sm font-semibold text-heading truncate">{{ testimonial.name }}</p>
        <p class="text-xs text-muted truncate">{{ testimonial.title }}</p>
      </div>
    </div>
  </div>
</template>
