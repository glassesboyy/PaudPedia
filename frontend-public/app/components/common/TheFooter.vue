<script setup lang="ts">
/**
 * TheFooter — Global footer component.
 *
 * Displays navigation links, social media, contact info, and copyright.
 * Data comes from landing page API (settings) passed via props,
 * with static fallbacks for navigation links.
 * Uses design system tokens exclusively.
 */
import type { ContactInfo, FooterData, SocialMedia } from '~~/types';

interface Props {
  contact?: ContactInfo | null
  social?: SocialMedia | null
  footer?: FooterData | null
}

withDefaults(defineProps<Props>(), {
  contact: null,
  social: null,
  footer: null,
})

const currentYear = new Date().getFullYear()

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
            PaudPedia
          </NuxtLink>
          <p class="mt-3 text-sm text-body leading-relaxed max-w-xs">
            {{ footer?.description || 'Platform e-learning dan marketplace untuk pendidikan anak usia dini (PAUD).' }}
          </p>

          <!-- Social media -->
          <div v-if="social" class="flex items-center gap-3 mt-5">
            <template v-for="(url, platform) in social" :key="platform">
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
        <div v-if="contact">
          <h3 class="text-sm font-semibold text-heading mb-4">Hubungi Kami</h3>
          <ul class="space-y-3">
            <li v-if="contact.email" class="flex items-start gap-2.5">
              <Icon name="lucide:mail" class="w-4 h-4 text-muted shrink-0 mt-0.5" />
              <a
                :href="`mailto:${contact.email}`"
                class="text-sm text-body hover:text-primary-600 transition-colors duration-200 break-all"
              >
                {{ contact.email }}
              </a>
            </li>
            <li v-if="contact.phone" class="flex items-start gap-2.5">
              <Icon name="lucide:phone" class="w-4 h-4 text-muted shrink-0 mt-0.5" />
              <a
                :href="`tel:${contact.phone}`"
                class="text-sm text-body hover:text-primary-600 transition-colors duration-200"
              >
                {{ contact.phone }}
              </a>
            </li>
            <li v-if="contact.address" class="flex items-start gap-2.5">
              <Icon name="lucide:map-pin" class="w-4 h-4 text-muted shrink-0 mt-0.5" />
              <span class="text-sm text-body">{{ contact.address }}</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="mt-10 pt-6 border-t border-border">
        <p class="text-center text-xs text-muted">
          {{ footer?.copyright || `© ${currentYear} PaudPedia. All rights reserved.` }}
        </p>
      </div>
    </div>
  </footer>
</template>
