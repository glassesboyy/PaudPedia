<script setup lang="ts">
/**
 * LMS Layout
 *
 * Layout for the course player / learning pages.
 * Optimized for content consumption with minimal chrome.
 */
import { useSiteSettingsStore } from '~~/stores/siteSettings';

const siteSettings = useSiteSettingsStore()

onMounted(() => {
  siteSettings.fetch()
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-surface-sunken">
    <!-- Slim top bar -->
    <header class="sticky top-0 z-50 border-b border-border bg-surface/95 backdrop-blur-sm h-14 flex items-center px-4 gap-4">
      <NuxtLink
        to="/account/courses"
        class="inline-flex items-center gap-1.5 text-xs font-medium text-muted hover:text-primary-600 transition-colors shrink-0"
      >
        <Icon name="lucide:arrow-left" class="w-4 h-4" />
        <span class="hidden sm:inline">Kursus Saya</span>
      </NuxtLink>

      <div class="h-5 w-px bg-border" />

      <NuxtLink to="/" class="font-bold text-primary-600 shrink-0">
        {{ siteSettings.siteName }}
      </NuxtLink>
    </header>

    <main class="flex-1">
      <slot />
    </main>
  </div>
</template>
