<script setup lang="ts">
/**
 * TestimonialsSection — Displays featured testimonials on landing page.
 *
 * Receives testimonial data via props from the parent landing page.
 * Uses design system tokens exclusively.
 */
import type { Testimonial } from '~~/types';

defineProps<{
  testimonials: Testimonial[]
}>()
</script>

<template>
  <SectionWrapper
    title="Apa Kata Mereka?"
    subtitle="Cerita inspiratif dari para pendidik dan orang tua yang telah bergabung bersama PaudPedia."
    bg="bg-surface"
  >
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <TestimonialCard
        v-for="testimonial in testimonials"
        :key="testimonial.id"
        :testimonial="testimonial"
      />
    </div>
  </SectionWrapper>
</template>
