<script setup lang="ts">
/**
 * Default Layout
 *
 * Used for all public pages (home, courses, articles, etc.)
 * Provides header (via TheNavbar), main content area, and footer (TheFooter).
 * Footer contact/social data is fetched on first load and cached.
 */
import type { ContactInfo, FooterData, SocialMedia } from '~~/types'

const footerContact = ref<ContactInfo | null>(null)
const footerSocial = ref<SocialMedia | null>(null)
const footerData = ref<FooterData | null>(null)

onMounted(async () => {
  try {
    const { landingService } = await import('~~/services')
    const res = await landingService.getData()
    if (res.success && res.data?.settings) {
      const s = res.data.settings
      footerContact.value = s.contact ?? null
      footerSocial.value = s.social_media ?? null
      footerData.value = s.footer ?? null
    }
  }
  catch {
    // Footer will render with static fallbacks
  }
})
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <TheNavbar show-nav />

    <main class="flex-1">
      <slot />
    </main>

    <TheFooter
      :contact="footerContact"
      :social="footerSocial"
      :footer="footerData"
    />
  </div>
</template>