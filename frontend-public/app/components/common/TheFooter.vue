<script setup lang="ts">
/**
 * TheFooter — Global footer component.
 *
 * Displays navigation links, social media, contact info, and copyright.
 * Data comes from the siteSettings store (fetched from the landing API).
 * Uses design system tokens exclusively.
 */
import { useSiteSettingsStore } from '~~/stores/siteSettings';

const siteSettings = useSiteSettingsStore()

const footerLinks = {
  platform: [
    { label: 'Kursus', to: '/courses' },
    { label: 'Webinar', to: '/webinars' },
    { label: 'Artikel', to: '/articles' },
    { label: 'Produk', to: '/products' },
  ],
  company: [
    { label: 'Tentang Kami', to: '/about' },
    { label: 'Kontak', to: '/contact' },
    { label: 'FAQ', to: '/faq' },
  ],
  legal: [
    { label: 'Kebijakan Privasi', to: '/privacy-policy' },
    { label: 'Syarat & Ketentuan', to: '/terms' },
  ],
}

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
  <footer class="border-t border-border bg-surface-muted">
    <div class="container py-12 sm:py-16">
      <!-- Main grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
        <!-- Brand column -->
        <div class="sm:col-span-2 lg:col-span-1">
          <NuxtLink to="/" class="text-2xl font-bold text-primary-600">
            {{ siteSettings.siteName }}
          </NuxtLink>
          <p class="mt-3 text-sm text-body leading-relaxed max-w-xs">
            {{ siteSettings.siteDescription }}
          </p>

          <!-- Social media -->
          <div v-if="Object.keys(siteSettings.socialMedia).length" class="flex flex-wrap gap-3 mt-5">
            <template v-for="(url, platform) in siteSettings.socialMedia" :key="platform">
              <a
                v-if="url && socialIcons[platform]"
                :href="url"
                target="_blank"
                rel="noopener noreferrer"
                class="w-9 h-9 rounded-full bg-surface flex items-center justify-center text-muted hover:text-primary-600 hover:bg-primary-50 border border-border transition-colors duration-200"
                :aria-label="String(platform)"
              >
                <Icon :name="socialIcons[platform]" class="w-4 h-4" />
              </a>
            </template>
          </div>
        </div>

        <!-- Platform links -->
        <div>
          <h3 class="text-sm font-semibold text-heading mb-4">Platform</h3>
          <ul class="space-y-2.5">
            <li v-for="link in footerLinks.platform" :key="link.to">
              <NuxtLink
                :to="link.to"
                class="text-sm text-body hover:text-primary-600 transition-colors duration-200"
              >
                {{ link.label }}
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Company links -->
        <div>
          <h3 class="text-sm font-semibold text-heading mb-4">Perusahaan</h3>
          <ul class="space-y-2.5">
            <li v-for="link in footerLinks.company" :key="link.to">
              <NuxtLink
                :to="link.to"
                class="text-sm text-body hover:text-primary-600 transition-colors duration-200"
              >
                {{ link.label }}
              </NuxtLink>
            </li>
            <li v-for="link in footerLinks.legal" :key="link.to">
              <NuxtLink
                :to="link.to"
                class="text-sm text-body hover:text-primary-600 transition-colors duration-200"
              >
                {{ link.label }}
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Contact info -->
        <div v-if="siteSettings.contact.email || siteSettings.contact.phone || siteSettings.contact.address">
          <h3 class="text-sm font-semibold text-heading mb-4">Hubungi Kami</h3>
          <ul class="space-y-3">
            <li v-if="siteSettings.contact.email" class="flex items-start gap-2.5">
              <Icon name="lucide:mail" class="w-4 h-4 text-muted shrink-0 mt-0.5" />
              <a
                :href="`mailto:${siteSettings.contact.email}`"
                class="text-sm text-body hover:text-primary-600 transition-colors duration-200 break-all"
              >
                {{ siteSettings.contact.email }}
              </a>
            </li>
            <li v-if="siteSettings.contact.phone" class="flex items-start gap-2.5">
              <Icon name="lucide:phone" class="w-4 h-4 text-muted shrink-0 mt-0.5" />
              <a
                :href="`tel:${siteSettings.contact.phone}`"
                class="text-sm text-body hover:text-primary-600 transition-colors duration-200"
              >
                {{ siteSettings.contact.phone }}
              </a>
            </li>
            <li v-if="siteSettings.contact.address" class="flex items-start gap-2.5">
              <Icon name="lucide:map-pin" class="w-4 h-4 text-muted shrink-0 mt-0.5" />
              <span class="text-sm text-body">{{ siteSettings.contact.address }}</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="mt-10 pt-6 border-t border-border">
        <p class="text-center text-xs text-muted">
          {{ siteSettings.footerCopyright }}
        </p>
      </div>
    </div>
  </footer>
</template>
