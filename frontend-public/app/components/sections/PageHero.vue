<script setup lang="ts">
/**
 * PageHero — Reusable hero section for static pages.
 *
 * Supports multiple visual variants with distinct decorative elements:
 * - default : Warm gradient with floating geometric shapes
 * - gradient: Bold colorful gradient with wave & orb decorations
 * - pattern : Dot pattern overlay with geometric corner accents
 *
 * Badge styling matches the landing page FeaturesSection badge pattern.
 */

interface Props {
  /** Badge label text */
  badge: string
  /** Badge icon (lucide icon name) */
  badgeIcon?: string
  /** Main heading text */
  title: string
  /** Colored accent text appended to title */
  highlight?: string
  /** Subtitle / description */
  description?: string
  /** Visual variant */
  variant?: 'default' | 'gradient' | 'pattern'
  /** Optional "last updated" date string */
  lastUpdated?: string
}

withDefaults(defineProps<Props>(), {
  badgeIcon: 'lucide:info',
  highlight: '',
  description: '',
  variant: 'default',
  lastUpdated: '',
})
</script>

<template>
  <section class="relative overflow-hidden">
    <!-- ============================================================ -->
    <!-- VARIANT: default — warm gradient + floating geometric shapes -->
    <!-- ============================================================ -->
    <template v-if="variant === 'default'">
      <!-- Base gradient -->
      <div class="absolute inset-0 bg-gradient-to-br from-primary-100/60 via-surface to-secondary-100/50" />
      <!-- Soft radial accent -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(var(--color-primary-200),.35),transparent)]" />

      <!-- Floating shapes -->
      <div class="absolute top-[10%] left-[6%] w-20 h-20 rounded-2xl bg-primary-300/15 rotate-12 blur-sm" />
      <div class="absolute top-[15%] right-[8%] w-14 h-14 rounded-full border-[3px] border-secondary-300/20" />
      <div class="absolute bottom-[18%] left-[12%] w-10 h-10 rounded-full bg-secondary-200/20 blur-[2px]" />
      <div class="absolute bottom-[12%] right-[10%] w-24 h-24 rounded-3xl bg-primary-200/15 -rotate-6 blur-sm" />
      <div class="absolute top-[45%] left-[3%] w-6 h-6 rounded-full bg-warning-300/20" />
      <div class="absolute top-[30%] right-[4%] w-8 h-8 rounded-lg bg-success-200/15 rotate-45" />

      <!-- Large glow orbs -->
      <div class="absolute -top-10 left-1/4 w-56 h-56 rounded-full bg-primary-200/20 blur-3xl" />
      <div class="absolute -bottom-12 right-1/4 w-48 h-48 rounded-full bg-secondary-200/20 blur-3xl" />
    </template>

    <!-- ============================================================ -->
    <!-- VARIANT: gradient — bold colorful gradient + waves & orbs    -->
    <!-- ============================================================ -->
    <template v-else-if="variant === 'gradient'">
      <!-- Bold gradient -->
      <div class="absolute inset-0 bg-gradient-to-br from-primary-200/70 via-primary-100/50 to-secondary-200/60" />
      <!-- Overlay glow at top -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_60%_40%_at_50%_0%,rgba(var(--color-primary-300),.3),transparent)]" />

      <!-- Wave decoration at bottom -->
      <svg class="absolute bottom-0 left-0 w-full h-16 text-surface" viewBox="0 0 1440 64" fill="none" preserveAspectRatio="none">
        <path d="M0 32C240 0 480 64 720 32C960 0 1200 64 1440 32V64H0V32Z" fill="currentColor" />
      </svg>

      <!-- Gradient orbs -->
      <div class="absolute top-[8%] left-[5%] w-32 h-32 rounded-full bg-primary-300/25 blur-2xl" />
      <div class="absolute top-[10%] right-[6%] w-40 h-40 rounded-full bg-secondary-300/20 blur-2xl" />
      <div class="absolute bottom-[20%] left-1/3 w-24 h-24 rounded-full bg-warning-200/15 blur-xl" />

      <!-- Floating rings -->
      <div class="absolute top-[20%] left-[15%] w-12 h-12 rounded-full border-2 border-white/20" />
      <div class="absolute top-[25%] right-[12%] w-8 h-8 rounded-full border-2 border-primary-400/20" />
      <div class="absolute bottom-[25%] right-[18%] w-16 h-16 rounded-full border-2 border-secondary-300/15" />

      <!-- Small diamonds -->
      <div class="absolute top-[40%] left-[8%] w-5 h-5 bg-white/10 rotate-45" />
      <div class="absolute bottom-[30%] right-[7%] w-4 h-4 bg-primary-200/20 rotate-45" />
    </template>

    <!-- ============================================================ -->
    <!-- VARIANT: pattern — dot grid + geometric corner accents       -->
    <!-- ============================================================ -->
    <template v-else-if="variant === 'pattern'">
      <!-- Base gradient -->
      <div class="absolute inset-0 bg-gradient-to-br from-primary-50/80 via-surface to-secondary-50/60" />

      <!-- Dot pattern overlay -->
      <div
        class="absolute inset-0 opacity-[0.06]"
        style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 20px 20px"
      />

      <!-- Corner geometric accents -->
      <svg class="absolute top-0 left-0 w-40 h-40 text-primary-200/30" viewBox="0 0 160 160" fill="none">
        <circle cx="0" cy="0" r="120" stroke="currentColor" stroke-width="1.5" />
        <circle cx="0" cy="0" r="80" stroke="currentColor" stroke-width="1" />
        <circle cx="0" cy="0" r="40" stroke="currentColor" stroke-width="0.5" />
      </svg>
      <svg class="absolute bottom-0 right-0 w-36 h-36 text-secondary-200/25" viewBox="0 0 144 144" fill="none">
        <circle cx="144" cy="144" r="100" stroke="currentColor" stroke-width="1.5" />
        <circle cx="144" cy="144" r="60" stroke="currentColor" stroke-width="1" />
      </svg>

      <!-- Scattered accent shapes -->
      <div class="absolute top-[15%] right-[10%] w-16 h-16 rounded-2xl bg-primary-200/10 rotate-12" />
      <div class="absolute bottom-[20%] left-[8%] w-12 h-12 rounded-xl bg-secondary-200/10 -rotate-6" />
      <div class="absolute top-[35%] left-[4%] w-5 h-5 rounded-full bg-primary-300/15" />
      <div class="absolute top-[25%] right-[5%] w-4 h-4 rounded-full bg-warning-200/20" />

      <!-- Subtle glow -->
      <div class="absolute top-0 left-1/3 w-64 h-40 rounded-full bg-primary-200/10 blur-3xl" />
      <div class="absolute bottom-0 right-1/4 w-48 h-32 rounded-full bg-secondary-200/10 blur-3xl" />
    </template>

    <!-- ─── Content (shared across all variants) ─── -->
    <div class="container py-16 sm:py-24 text-center relative z-10">
      <!-- Badge -->
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-6">
        <Icon :name="badgeIcon" class="w-3.5 h-3.5 text-primary-500" />
        <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">{{ badge }}</span>
      </div>

      <!-- Title -->
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-heading leading-tight">
        {{ title }}
        <span v-if="highlight" class="text-primary-600"> {{ highlight }}</span>
      </h1>

      <!-- Description -->
      <p v-if="description" class="mt-4 sm:mt-5 text-base sm:text-lg text-body max-w-2xl mx-auto leading-relaxed">
        {{ description }}
      </p>

      <!-- Last Updated -->
      <div v-if="lastUpdated" class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-surface/80 border border-border text-xs text-muted">
        <Icon name="lucide:calendar" class="w-3.5 h-3.5" />
        Terakhir diperbarui: {{ lastUpdated }}
      </div>

      <!-- Extra slot -->
      <slot />
    </div>
  </section>
</template>
