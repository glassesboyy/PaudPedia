<script setup lang="ts">
/**
 * AuthCarousel — Auto-rotating image carousel for the auth split layout.
 *
 * Uses design system tokens for all styling.
 * Images are sourced from public/assets/auth/.
 */
import { useSiteSettingsStore } from '~~/stores/siteSettings'

const siteSettings = useSiteSettingsStore()

const slides = [
  {
    src: '/assets/auth/auth-carousel-1.jpg',
    alt: 'Anak-anak belajar bersama di PaudPedia',
  },
  {
    src: '/assets/auth/auth-carousel-2.jpg',
    alt: 'Platform pembelajaran PAUD interaktif',
  },
  {
    src: '/assets/auth/auth-carousel-3.jpg',
    alt: 'Komunitas pendidik PAUD',
  },
]

const currentIndex = ref(0)
let intervalId: ReturnType<typeof setInterval> | null = null

function goTo(index: number) {
  currentIndex.value = index
  resetAutoplay()
}

function resetAutoplay() {
  if (intervalId) clearInterval(intervalId)
  intervalId = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % slides.length
  }, 5000)
}

onMounted(() => {
  resetAutoplay()
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>

<template>
  <div class="relative h-full w-full overflow-hidden rounded-2xl bg-surface-sunken">
    <!-- Slides -->
    <div class="relative h-full">
      <TransitionGroup
        enter-active-class="transition-opacity duration-700 ease-smooth"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-500 ease-smooth absolute inset-0"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <NuxtImg
          v-for="(slide, index) in slides"
          v-show="currentIndex === index"
          :key="slide.src"
          :src="slide.src"
          :alt="slide.alt"
          class="h-full w-full object-cover"
          loading="lazy"
          format="webp"
          quality="85"
        />
      </TransitionGroup>
    </div>

    <!-- Gradient overlay for readability -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none" />

    <!-- Bottom content -->
    <div class="absolute bottom-0 left-0 right-0 p-6">
      <!-- Branding tagline -->
      <div class="mb-4">
        <h2 class="text-xl font-bold text-white">{{ siteSettings.siteName }}</h2>
        <p class="mt-1 text-sm text-white/80">
          {{ siteSettings.siteTagline }}
        </p>
      </div>

      <!-- Dot indicators -->
      <div class="flex gap-2">
        <button
          v-for="(_, index) in slides"
          :key="index"
          class="h-1.5 rounded-full transition-all duration-300"
          :class="[
            currentIndex === index
              ? 'w-6 bg-white'
              : 'w-1.5 bg-white/50 hover:bg-white/70',
          ]"
          :aria-label="`Slide ${index + 1}`"
          @click="goTo(index)"
        />
      </div>
    </div>
  </div>
</template>
