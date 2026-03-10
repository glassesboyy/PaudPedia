<script setup lang="ts">
/**
 * MentorCard — Displays a single mentor's profile card.
 *
 * Used on the Mentors listing page and About page.
 * Links to the mentor detail page via numeric ID.
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

/** Parse comma-separated expertise string into array */
function parseExpertise(expertise: string | string[] | undefined): string[] {
  if (!expertise) return []
  if (Array.isArray(expertise)) return expertise
  return expertise.split(',').map(s => s.trim()).filter(Boolean)
}
</script>

<template>
  <NuxtLink
    :to="`/mentors/${mentor.id}`"
    class="group block text-center p-6 rounded-2xl border border-border bg-surface hover:border-primary-200 hover:shadow-medium hover:-translate-y-1 transition-all duration-300"
  >
    <!-- Avatar -->
    <div class="relative w-20 h-20 mx-auto mb-4">
      <img
        v-if="mentor.photo_url"
        :src="mentor.photo_url"
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

    <!-- Title -->
    <p v-if="mentor.title" class="text-xs text-muted mt-0.5">
      {{ mentor.title }}
    </p>

    <!-- Expertise Tags -->
    <div v-if="mentor.expertise" class="mt-2 flex flex-wrap justify-center gap-1">
      <span
        v-for="skill in parseExpertise(mentor.expertise).slice(0, 2)"
        :key="skill"
        class="inline-block px-2 py-0.5 text-[10px] font-medium rounded-full bg-primary-50 text-primary-600"
      >
        {{ skill }}
      </span>
    </div>

    <!-- Bio -->
    <p v-if="mentor.bio" class="text-xs text-body mt-3 leading-relaxed line-clamp-2">{{ mentor.bio }}</p>

    <!-- Stats -->
    <div class="mt-3 flex items-center justify-center gap-4 text-xs text-muted">
      <span class="flex items-center gap-1">
        <Icon name="lucide:book-open" class="w-3 h-3" />
        {{ mentor.courses_count }} Kursus
      </span>
      <span class="flex items-center gap-1">
        <Icon name="lucide:video" class="w-3 h-3" />
        {{ mentor.webinars_count }} Webinar
      </span>
    </div>

    <!-- Social Media (only for detail-like contexts if social_media exists) -->
    <div
      v-if="(mentor as any).social_media && Object.keys((mentor as any).social_media).length"
      class="mt-3 flex justify-center gap-2"
      @click.prevent
    >
      <template v-for="(url, platform) in (mentor as any).social_media" :key="platform">
        <a
          v-if="url"
          :href="String(url).startsWith('http') ? String(url) : `https://${String(url)}`"
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
