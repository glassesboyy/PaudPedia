<script setup lang="ts">
/**
 * FeaturedWebinarsSection — Showcases featured webinars on landing page.
 *
 * Receives webinar data via props from the parent landing page.
 * Uses design system tokens exclusively.
 */
import type { Webinar } from '~~/types';

defineProps<{
  webinars: Webinar[]
}>()
</script>

<template>
  <section class="relative w-full bg-surface overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-secondary-50/40 translate-x-1/3 -translate-y-1/3" />
    <div class="absolute bottom-0 left-0 w-72 h-72 rounded-full bg-primary-50/30 -translate-x-1/4 translate-y-1/4" />
    <div class="absolute top-1/2 right-[10%] w-1 h-40 bg-border-muted/40 rounded-full" />

    <div class="container py-14 sm:py-20 relative z-10">
      <!-- Section header with badge -->
      <div class="text-center mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary-50 border border-secondary-100 mb-4">
          <Icon name="lucide:calendar" class="w-3.5 h-3.5 text-secondary-500" />
          <span class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">Acara Online</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-heading">
          Webinar Mendatang
        </h2>
        <p class="mt-3 text-base text-body max-w-2xl mx-auto">
          Ikuti sesi webinar interaktif bersama para ahli pendidikan anak usia dini.
        </p>
      </div>

      <!-- Webinar cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <WebinarCard
          v-for="webinar in webinars"
          :key="webinar.id"
          :webinar="webinar"
        />
      </div>

      <!-- Footer -->
      <div v-if="webinars.length" class="mt-10 text-center">
        <NuxtLink to="/webinars">
          <UButton variant="outline" size="md" class="group/btn">
            Lihat Semua Webinar
            <Icon name="lucide:arrow-right" class="w-4 h-4 ml-1.5 group-hover/btn:translate-x-1 transition-transform duration-200" />
          </UButton>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
