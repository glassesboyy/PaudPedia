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
  <SectionWrapper
    title="Webinar Mendatang"
    subtitle="Ikuti sesi webinar interaktif bersama para ahli pendidikan anak usia dini."
    bg="bg-surface"
  >
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <WebinarCard
        v-for="webinar in webinars"
        :key="webinar.id"
        :webinar="webinar"
      />
    </div>

    <template v-if="webinars.length" #footer>
      <NuxtLink to="/webinars">
        <UButton variant="outline" size="md">
          Lihat Semua Webinar
          <Icon name="lucide:arrow-right" class="w-4 h-4 ml-1.5" />
        </UButton>
      </NuxtLink>
    </template>
  </SectionWrapper>
</template>
