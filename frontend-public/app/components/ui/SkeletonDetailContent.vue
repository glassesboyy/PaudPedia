<script setup lang="ts">
/**
 * SkeletonDetailContent — Skeleton for detail/single-content pages.
 *
 * @prop variant - Layout type: 'article' | 'profile' | 'default' (default: 'default')
 *
 * Variants:
 * - article:  breadcrumb, title, subtitle, author row, hero image, paragraph lines
 * - profile:  hero section (avatar + info) + body section (content + sidebar)
 * - default:  generic content lines
 */

interface Props {
  variant?: 'article' | 'profile' | 'default'
}

withDefaults(defineProps<Props>(), {
  variant: 'default',
})
</script>

<template>
  <!-- Article detail skeleton -->
  <div v-if="variant === 'article'" class="bg-surface">
    <div class="container py-10">
      <div class="max-w-3xl mx-auto animate-pulse">
        <div class="h-4 w-32 bg-muted/10 rounded mb-6" />
        <div class="h-8 w-3/4 bg-muted/10 rounded mb-4" />
        <div class="h-6 w-1/2 bg-muted/10 rounded mb-8" />
        <div class="flex items-center gap-3 mb-8">
          <div class="w-10 h-10 rounded-full bg-muted/10" />
          <div>
            <div class="h-4 w-24 bg-muted/10 rounded mb-1" />
            <div class="h-3 w-32 bg-muted/10 rounded" />
          </div>
        </div>
        <div class="aspect-[2/1] rounded-2xl bg-muted/10 mb-8" />
        <div class="space-y-4">
          <div class="h-4 w-full bg-muted/10 rounded" />
          <div class="h-4 w-full bg-muted/10 rounded" />
          <div class="h-4 w-5/6 bg-muted/10 rounded" />
          <div class="h-4 w-full bg-muted/10 rounded" />
          <div class="h-4 w-3/4 bg-muted/10 rounded" />
        </div>
      </div>
    </div>
  </div>

  <!-- Profile detail skeleton (mentor detail) -->
  <div v-else-if="variant === 'profile'" class="min-h-[60vh]">
    <!-- Hero skeleton -->
    <div class="bg-gradient-to-br from-primary-100/60 via-surface to-secondary-100/50">
      <div class="container py-12 sm:py-16">
        <div class="flex flex-col md:flex-row gap-8 items-center md:items-start animate-pulse">
          <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-3xl bg-muted/15 shrink-0" />
          <div class="flex-1 text-center md:text-left space-y-3 w-full">
            <div class="h-4 w-24 bg-muted/10 rounded-full mx-auto md:mx-0" />
            <div class="h-8 w-3/4 bg-muted/10 rounded mx-auto md:mx-0" />
            <div class="h-5 w-1/2 bg-muted/10 rounded mx-auto md:mx-0" />
            <div class="flex gap-2 justify-center md:justify-start">
              <div class="h-6 w-24 bg-muted/10 rounded-full" />
              <div class="h-6 w-20 bg-muted/10 rounded-full" />
            </div>
            <div class="flex gap-4 justify-center md:justify-start">
              <div class="h-5 w-20 bg-muted/10 rounded" />
              <div class="h-5 w-20 bg-muted/10 rounded" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Body skeleton -->
    <div class="container py-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-pulse">
        <div class="lg:col-span-2 space-y-4">
          <div class="h-6 w-32 bg-muted/10 rounded" />
          <div class="h-4 w-full bg-muted/10 rounded" />
          <div class="h-4 w-full bg-muted/10 rounded" />
          <div class="h-4 w-3/4 bg-muted/10 rounded" />
        </div>
        <div class="space-y-4">
          <div class="h-6 w-32 bg-muted/10 rounded" />
          <div class="h-10 w-full bg-muted/10 rounded-xl" />
          <div class="h-10 w-full bg-muted/10 rounded-xl" />
        </div>
      </div>
    </div>
  </div>

  <!-- Default skeleton -->
  <div v-else class="container py-10">
    <div class="max-w-3xl mx-auto animate-pulse space-y-4">
      <div class="h-8 w-1/2 bg-muted/10 rounded mb-6" />
      <div class="h-4 w-full bg-muted/10 rounded" />
      <div class="h-4 w-full bg-muted/10 rounded" />
      <div class="h-4 w-5/6 bg-muted/10 rounded" />
      <div class="h-4 w-3/4 bg-muted/10 rounded" />
      <div class="h-4 w-full bg-muted/10 rounded" />
    </div>
  </div>
</template>
