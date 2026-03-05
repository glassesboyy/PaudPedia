<script setup lang="ts">
/**
 * FeaturedCoursesSection — Showcases featured courses on landing page.
 *
 * Receives course data via props from the parent landing page.
 * Uses design system tokens exclusively.
 */
import type { Course } from '~~/types';

defineProps<{
  courses: Course[]
}>()
</script>

<template>
  <SectionWrapper
    title="Kursus Unggulan"
    subtitle="Pilihan kursus terbaik untuk meningkatkan kompetensi pendidikan anak usia dini."
    bg="bg-surface-muted"
  >
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <CourseCard
        v-for="course in courses"
        :key="course.id"
        :course="course"
      />
    </div>

    <template v-if="courses.length" #footer>
      <NuxtLink to="/courses">
        <UButton variant="outline" size="md">
          Lihat Semua Kursus
          <Icon name="lucide:arrow-right" class="w-4 h-4 ml-1.5" />
        </UButton>
      </NuxtLink>
    </template>
  </SectionWrapper>
</template>
