<script setup lang="ts">
/**
 * LMS Lesson Player
 * Route: /learn/:courseSlug/:lessonSlug
 */
definePageMeta({
  layout: 'lms',
  middleware: ['auth'],
})

const route = useRoute()
const _courseSlug = route.params.courseSlug as string
const _lessonSlug = route.params.lessonSlug as string

useSeo({ title: 'Belajar' })
</script>

<template>
  <div>
    <!-- TODO: CoursePlayer component -->
    <p class="p-8 text-gray-500">Lesson player akan diimplementasikan pada tahap development.</p>
  </div>
</template>
