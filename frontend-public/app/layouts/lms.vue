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
    <header class="border-b border-border bg-surface h-14 flex items-center px-4">
      <NuxtLink to="/" class="font-bold text-primary-600">
        {{ siteSettings.siteName }}
      </NuxtLink>
    </header>

    <main class="flex-1">
      <slot />
    </main>
  </div>
</template>

