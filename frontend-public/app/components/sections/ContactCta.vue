<script setup lang="ts">
/**
 * ContactCta — Reusable call-to-action section to encourage users to contact support.
 *
 * Used on FAQ, Privacy Policy, and Terms pages.
 * Provides better visual hierarchy than a simple inline CTA.
 */

interface Props {
  /** Heading text */
  title?: string
  /** Description text */
  description?: string
  /** Icon above the title */
  icon?: string
  /** CTA button text */
  buttonText?: string
  /** CTA button link */
  buttonLink?: string
}

withDefaults(defineProps<Props>(), {
  title: 'Masih punya pertanyaan?',
  description: 'Tim kami siap membantu Anda. Jangan ragu untuk menghubungi kami.',
  icon: 'lucide:message-circle',
  buttonText: 'Hubungi Kami',
  buttonLink: '/contact',
})
</script>

<template>
  <section class="bg-surface-muted">
    <div class="container py-12 sm:py-16">
      <div class="max-w-xl mx-auto text-center">
        <div class="w-12 h-12 rounded-2xl bg-primary-50 border border-primary-100 flex items-center justify-center mx-auto mb-4">
          <Icon :name="icon" class="w-6 h-6 text-primary-600" />
        </div>
        <h3 class="text-lg font-semibold text-heading mb-2">{{ title }}</h3>
        <p class="text-sm text-body mb-6 leading-relaxed">{{ description }}</p>
        <NuxtLink
          :to="buttonLink"
          class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 shadow-soft hover:shadow-medium transition-all duration-300"
        >
          <Icon name="lucide:mail" class="w-4 h-4" />
          {{ buttonText }}
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
