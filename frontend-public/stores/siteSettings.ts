/**
 * Site Settings Store
 *
 * Fetches site settings from the landing API and provides
 * reactive access to site_name, site_tagline, site_description,
 * contact info, and social media links across all components.
 *
 * Fetched once on first access and cached in memory.
 */
import { defineStore } from 'pinia'
import type { ContactInfo, SiteSettings, SocialMedia } from '~~/types'

export const useSiteSettingsStore = defineStore('siteSettings', () => {
  // ── State ──────────────────────────────────────────────
  const settings = ref<SiteSettings | null>(null)
  const isLoaded = ref(false)
  const isLoading = ref(false)

  // ── Getters ────────────────────────────────────────────
  const siteName = computed(() => settings.value?.site_name ?? 'PaudPedia')
  const siteTagline = computed(() => settings.value?.site_tagline ?? 'Platform Pendidikan Anak Usia Dini Terpadu')
  const siteDescription = computed(() => settings.value?.site_description ?? 'Platform e-learning dan marketplace untuk pendidikan anak usia dini (PAUD).')

  const contact = computed<ContactInfo>(() => settings.value?.contact ?? {
    email: null,
    phone: null,
    address: null,
  })

  const socialMedia = computed<SocialMedia>(() => settings.value?.social_media ?? {})

  const footerCopyright = computed(() => `© ${new Date().getFullYear()} ${siteName.value}. All rights reserved.`)

  // ── Actions ────────────────────────────────────────────
  let _fetchPromise: Promise<void> | null = null

  /**
   * Fetch site settings from the landing API.
   * De-duplicated — calling multiple times returns the same promise.
   */
  async function fetch() {
    if (isLoaded.value) return
    if (_fetchPromise) return _fetchPromise

    _fetchPromise = _doFetch()
    return _fetchPromise
  }

  async function _doFetch() {
    isLoading.value = true
    try {
      const { landingService } = await import('~~/services')
      const res = await landingService.getData()
      if (res.success && res.data?.settings) {
        settings.value = res.data.settings
      }
    }
    catch {
      // Will use fallback defaults
    }
    finally {
      isLoaded.value = true
      isLoading.value = false
    }
  }

  return {
    // State
    settings,
    isLoaded,
    isLoading,
    // Getters
    siteName,
    siteTagline,
    siteDescription,
    contact,
    socialMedia,
    footerCopyright,
    // Actions
    fetch,
  }
})
