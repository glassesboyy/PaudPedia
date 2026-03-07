<script setup lang="ts">
/**
 * MentorCard — Displays a single mentor's profile card.
 *
 * Used on the About page (Mentor Kami section) and Mentors listing page.
 * Links to the mentor detail page.
 */
import type { Mentor } from '~~/types';

interface Props {
  mentor: Mentor
}

defineProps<Props>()

const socialIcons: Record<string, string> = {
  instagram: 'lucide:instagram',
  facebook: 'lucide:facebook',
  youtube: 'lucide:youtube',
  linkedin: 'lucide:linkedin',
  twitter: 'lucide:twitter',
  tiktok: 'simple-icons:tiktok',
  telegram: 'lucide:send',
  discord: 'simple-icons:discord',
}
</script>

<template>
  <NuxtLink
    :to="`/mentors/${mentor.slug}`"
    class="group block text-center p-6 rounded-2xl border border-border bg-surface hover:border-primary-200 hover:shadow-medium hover:-translate-y-1 transition-all duration-300"
  >
    <!-- Avatar -->
    <div class="relative w-20 h-20 mx-auto mb-4">
      <img
        v-if="mentor.avatar_url"
        :src="mentor.avatar_url"
        :alt="mentor.name"
        class="w-full h-full rounded-2xl object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div v-else class="w-full h-full rounded-2xl bg-primary-50 flex items-center justify-center">
        <Icon name="lucide:user" class="w-8 h-8 text-primary-400" />
      </div>
    </div>

    <!-- Name -->
    <h3 class="text-sm font-semibold text-heading group-hover:text-primary-600 transition-colors">
      {{ mentor.name }}
    </h3>

    <!-- Expertise Tags -->
    <div v-if="mentor.expertise?.length" class="mt-1.5 flex flex-wrap justify-center gap-1">
      <span
        v-for="skill in mentor.expertise.slice(0, 2)"
        :key="skill"
        class="inline-block px-2 py-0.5 text-[10px] font-medium rounded-full bg-primary-50 text-primary-600"
      >
        {{ skill }}
      </span>
    </div>

    <!-- Bio -->
    <p class="text-xs text-body mt-3 leading-relaxed line-clamp-2">{{ mentor.bio }}</p>

    <!-- Stats -->
    <div class="mt-3 flex items-center justify-center gap-4 text-xs text-muted">
      <span class="flex items-center gap-1">
        <Icon name="lucide:book-open" class="w-3 h-3" />
        {{ mentor.total_courses }} Kursus
      </span>
      <span class="flex items-center gap-1">
        <Icon name="lucide:users" class="w-3 h-3" />
        {{ mentor.total_students }} Siswa
      </span>
    </div>

    <!-- Social Media -->
    <div
      v-if="mentor.social_media_links && Object.keys(mentor.social_media_links).length"
      class="mt-3 flex justify-center gap-2"
      @click.prevent
    >
      <template v-for="(url, platform) in mentor.social_media_links" :key="platform">
        <a
          v-if="url"
          :href="url"
          target="_blank"
          rel="noopener noreferrer"
          class="w-7 h-7 rounded-lg bg-surface-muted border border-border flex items-center justify-center text-muted hover:text-primary-600 hover:bg-primary-50 hover:border-primary-200 transition-all"
          :aria-label="String(platform)"
          @click.stop
        >
          <Icon :name="socialIcons[String(platform)] || 'lucide:link'" class="w-3.5 h-3.5" />
        </a>
      </template>
    </div>
  </NuxtLink>
</template>
