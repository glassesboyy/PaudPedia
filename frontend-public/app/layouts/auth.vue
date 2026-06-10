<script setup lang="ts">
/**
 * Auth Layout — Split Screen Design
 *
 * Desktop: left carousel (50%) + right form panel (50%)
 * Mobile:  full-width form only (carousel hidden)
 *
 * Uses design system tokens exclusively.
 */
import { useSiteSettingsStore } from '~~/stores/siteSettings';

const siteSettings = useSiteSettingsStore()

onMounted(() => {
  siteSettings.fetch()
})
</script>

<template>
  <div class="min-h-screen flex bg-surface-muted">
    <!-- Left panel: Carousel (hidden on mobile) -->
    <div class="hidden lg:block lg:w-1/2 xl:w-[55%] p-4">
      <AuthCarousel />
    </div>

    <!-- Right panel: Form area -->
    <div class="flex flex-1 flex-col lg:w-1/2 xl:w-[45%]">
      <!-- Top bar with logo + back link -->
      <div class="flex items-center justify-between p-4 sm:p-6">
        <NuxtLink 
          to="/" 
          class="text-xl font-bold text-primary-600"
          :class="{ 'pointer-events-none opacity-80': $route.path === '/auth/verify-email' }"
        >
          {{ siteSettings.siteName }}
        </NuxtLink>
        <NuxtLink
          v-if="$route.path !== '/auth/verify-email'"
          to="/"
          class="flex items-center gap-1 text-sm text-muted hover:text-foreground transition-colors"
        >
          <Icon name="lucide:arrow-left" class="w-4 h-4" />
          Kembali
        </NuxtLink>
      </div>

      <!-- Centered form container -->
      <div class="flex flex-1 items-center justify-center px-4 pb-8">
        <div class="w-full max-w-md">
          <div class="bg-surface rounded-xl shadow-card border border-border p-6 sm:p-8">
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
