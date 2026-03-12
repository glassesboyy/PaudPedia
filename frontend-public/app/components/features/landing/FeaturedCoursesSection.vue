<script setup lang="ts">
/**
 * FeaturedCoursesSection — Showcases featured courses on landing page.
 *
 * Receives course data via props from the parent landing page.
 * Uses design system tokens exclusively.
 */
import type { LandingCourse } from '~~/types';

defineProps<{
  courses: LandingCourse[]
}>()
</script>

<template>
  <section class="relative w-full bg-surface-muted overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute top-0 left-0 w-80 h-80 rounded-full bg-primary-50/40 -translate-x-1/3 -translate-y-1/3" />
    <div class="absolute bottom-0 right-0 w-64 h-64 rounded-full bg-secondary-50/30 translate-x-1/4 translate-y-1/4" />

    <div class="container py-14 sm:py-20 relative z-10">
      <!-- Section header with badge -->
      <div class="text-center mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
          <Icon name="lucide:graduation-cap" class="w-3.5 h-3.5 text-primary-500" />
          <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Program Pembelajaran</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-heading">
          Kursus Unggulan
        </h2>
        <p class="mt-3 text-base text-body max-w-2xl mx-auto">
          Pilihan kursus terbaik untuk meningkatkan kompetensi pendidikan anak usia dini.
        </p>
      </div>

      <!-- Course cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <CourseCard
          v-for="course in courses"
          :key="course.id"
          :course="course"
        />
      </div>

      <!-- Footer -->
      <div v-if="courses.length" class="mt-10 text-center">
        <NuxtLink to="/courses">
          <UButton variant="outline" size="md" class="group/btn">
            Lihat Semua Kursus
            <Icon name="lucide:arrow-right" class="w-4 h-4 ml-1.5 group-hover/btn:translate-x-1 transition-transform duration-200" />
          </UButton>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
