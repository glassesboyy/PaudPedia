<script setup lang="ts">
/**
 * HeroSection — Landing page hero banner.
 *
 * Displays headline, subtitle, CTA button and hero image.
 * Data comes from the landing API via props.
 */
import type { HeroData } from '~~/types';

interface Props {
  hero: HeroData | null
  siteName?: string
}

withDefaults(defineProps<Props>(), {
  siteName: 'PaudPedia',
})
</script>

<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-surface to-secondary-50">
    <div class="container py-16 sm:py-24">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
        <!-- Text content -->
        <div class="text-center lg:text-left">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-heading leading-tight">
            {{ hero?.title || `Selamat Datang di ${siteName}` }}
          </h1>
          <p class="mt-4 sm:mt-6 text-base sm:text-lg text-body leading-relaxed max-w-lg mx-auto lg:mx-0">
            {{ hero?.subtitle || 'Platform e-learning dan marketplace untuk pendidikan anak usia dini.' }}
          </p>
          <div class="mt-6 sm:mt-8 flex flex-wrap gap-3 justify-center lg:justify-start">
            <NuxtLink :to="hero?.cta_link || '/courses'">
              <UButton variant="primary" size="lg">
                {{ hero?.cta_text || 'Mulai Belajar' }}
              </UButton>
            </NuxtLink>
            <NuxtLink to="/about">
              <UButton variant="outline" size="lg">
                Pelajari Selengkapnya
              </UButton>
            </NuxtLink>
          </div>
        </div>

        <!-- Hero image -->
        <div class="flex justify-center lg:justify-end">
          <div class="relative w-full max-w-md lg:max-w-lg">
            <img
              v-if="hero?.image"
              :src="hero.image"
              :alt="hero?.title || 'PaudPedia'"
              class="w-full h-auto rounded-3xl shadow-elevated object-cover"
            />
            <!-- Fallback decorative illustration -->
            <div
              v-else
              class="w-full aspect-square rounded-3xl bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center shadow-elevated"
            >
              <div class="text-center px-8">
                <Icon name="lucide:graduation-cap" class="w-20 h-20 text-primary-400 mx-auto" />
                <p class="mt-4 text-lg font-semibold text-primary-600">{{ siteName }}</p>
                <p class="mt-1 text-sm text-body">Pendidikan Anak Usia Dini</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Decorative elements -->
    <div class="absolute top-10 left-10 w-20 h-20 rounded-full bg-primary-200/30 blur-xl" />
    <div class="absolute bottom-10 right-10 w-32 h-32 rounded-full bg-secondary-200/30 blur-xl" />
  </section>
</template>
