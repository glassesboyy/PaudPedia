<script setup lang="ts">
/**
 * Default Layout
 *
 * Used for all public pages (home, courses, articles, etc.)
 * Provides header (via TheNavbar), main content area, and footer (TheFooter).
 * Site settings are fetched via the siteSettings store.
 */
import { useSiteSettingsStore } from '~~/stores/siteSettings';

const siteSettings = useSiteSettingsStore()

onMounted(() => {
  siteSettings.fetch()
})
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <TheNavbar show-nav />

    <main class="flex-1">
      <slot />
    </main>

    <TheFooter />
  </div>
</template>