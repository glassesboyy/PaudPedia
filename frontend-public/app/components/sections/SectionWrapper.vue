<script setup lang="ts">
/**
 * SectionWrapper — Reusable page section layout atom.
 *
 * Provides consistent spacing, optional title/subtitle, and a content slot.
 * Uses design system tokens exclusively.
 */
interface Props {
  title?: string
  subtitle?: string
  /** Additional background classes (e.g. bg-surface-muted) */
  bg?: string
  /** Reduce vertical padding */
  compact?: boolean
}

withDefaults(defineProps<Props>(), {
  title: '',
  subtitle: '',
  bg: '',
  compact: false,
})
</script>

<template>
  <section :class="['w-full', bg]">
    <div :class="['container', compact ? 'py-10 sm:py-14' : 'py-14 sm:py-20']">
      <!-- Section header -->
      <div v-if="title || $slots.header" class="text-center mb-10 sm:mb-14">
        <slot name="header">
          <h2 class="text-2xl sm:text-3xl font-bold text-heading">
            {{ title }}
          </h2>
          <p v-if="subtitle" class="mt-3 text-base text-body max-w-2xl mx-auto">
            {{ subtitle }}
          </p>
        </slot>
      </div>

      <!-- Content -->
      <slot />

      <!-- Optional footer slot (e.g. "View all" button) -->
      <div v-if="$slots.footer" class="mt-10 text-center">
        <slot name="footer" />
      </div>
    </div>
  </section>
</template>
