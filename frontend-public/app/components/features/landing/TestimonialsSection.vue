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
  <section class="relative w-full bg-surface-muted overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute top-0 left-[10%] w-64 h-64 rounded-full bg-warning-50/30 -translate-y-1/3" />
    <div class="absolute bottom-0 right-[10%] w-56 h-56 rounded-full bg-primary-50/30 translate-y-1/3" />
    <div class="absolute top-[20%] right-0 w-1 h-32 bg-border-muted/30 rounded-full" />
    <div class="absolute bottom-[20%] left-0 w-1 h-32 bg-border-muted/30 rounded-full" />

    <div class="container py-14 sm:py-20 relative z-10">
      <!-- Section header with badge -->
      <div class="text-center mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-warning-50 border border-warning-100 mb-4">
          <Icon name="lucide:message-circle" class="w-3.5 h-3.5 text-warning-500" />
          <span class="text-xs font-semibold text-warning-600 uppercase tracking-wider">Testimoni</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-heading">
          Apa Kata Mereka?
        </h2>
        <p class="mt-3 text-base text-body max-w-2xl mx-auto">
          Cerita inspiratif dari para pendidik dan orang tua yang telah bergabung bersama PaudPedia.
        </p>
      </div>

      <!-- Testimonial cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <TestimonialCard
          v-for="testimonial in testimonials"
          :key="testimonial.id"
          :testimonial="testimonial"
        />
      </div>
    </div>
  </section>
</template>
