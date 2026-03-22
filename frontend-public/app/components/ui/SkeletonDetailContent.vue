<script setup lang="ts">
/**
 * SkeletonDetailContent — Skeleton for detail/single-content pages.
 *
 * @prop variant - Layout type matching specific page structures
 *
 * Variants:
 * - article:  breadcrumb, title, subtitle, author row, hero image, paragraph lines
 * - profile:  hero section (avatar + info) + body section (content + sidebar)
 * - course:   2-col hero (image + info cards), syllabus accordion, description
 * - product:  2-col hero (image + file info card), description
 * - webinar:  hero (thumbnail + title/schedule), 3-col body (description + sidebar)
 * - default:  generic content lines
 */

interface Props {
  variant?: 'article' | 'profile' | 'course' | 'product' | 'webinar' | 'default'
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

  <!-- Course detail skeleton — matches 2-col hero + syllabus accordion layout -->
  <div v-else-if="variant === 'course'">
    <!-- Hero section -->
    <div class="bg-gradient-to-br from-primary-50 via-surface to-secondary-50/30">
      <div class="container pt-8 pb-12 sm:pt-10 sm:pb-16">
        <div class="max-w-5xl mx-auto animate-pulse">
          <!-- Breadcrumb -->
          <div class="flex items-center gap-2 mb-6">
            <div class="h-3 w-14 bg-muted/10 rounded" />
            <div class="h-3 w-3 bg-muted/10 rounded" />
            <div class="h-3 w-12 bg-muted/10 rounded" />
            <div class="h-3 w-3 bg-muted/10 rounded" />
            <div class="h-3 w-20 bg-muted/10 rounded" />
          </div>
          <!-- 2-col hero -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Image -->
            <div class="aspect-video rounded-2xl bg-muted/10" />
            <!-- Info -->
            <div class="space-y-4">
              <!-- Category + Level badges -->
              <div class="flex items-center gap-2">
                <div class="h-6 w-20 bg-muted/10 rounded-full" />
                <div class="h-6 w-16 bg-muted/10 rounded-full" />
              </div>
              <!-- Title -->
              <div class="h-8 w-full bg-muted/10 rounded" />
              <div class="h-8 w-2/3 bg-muted/10 rounded" />
              <!-- Price -->
              <div class="h-10 w-32 bg-muted/10 rounded" />
              <!-- Info card (4 stats) -->
              <div class="rounded-xl border border-border/30 p-5 space-y-3">
                <div class="h-3 w-24 bg-muted/10 rounded" />
                <div class="grid grid-cols-2 gap-4">
                  <div v-for="n in 4" :key="n" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-muted/10" />
                    <div class="space-y-1">
                      <div class="h-2.5 w-12 bg-muted/10 rounded" />
                      <div class="h-3.5 w-20 bg-muted/10 rounded" />
                    </div>
                  </div>
                </div>
              </div>
              <!-- Mentor card -->
              <div class="rounded-xl border border-border/30 p-5">
                <div class="h-3 w-16 bg-muted/10 rounded mb-4" />
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 rounded-xl bg-muted/10" />
                  <div class="space-y-1.5 flex-1">
                    <div class="h-4 w-28 bg-muted/10 rounded" />
                    <div class="h-3 w-36 bg-muted/10 rounded" />
                  </div>
                </div>
              </div>
              <!-- CTA button -->
              <div class="h-12 w-full bg-muted/10 rounded-xl" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Syllabus section -->
    <div class="bg-surface">
      <div class="container py-8 sm:py-12">
        <div class="max-w-5xl mx-auto animate-pulse">
          <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border/20">
            <div class="w-9 h-9 rounded-lg bg-muted/10" />
            <div class="space-y-1">
              <div class="h-6 w-36 bg-muted/10 rounded" />
              <div class="h-3 w-48 bg-muted/10 rounded" />
            </div>
          </div>
          <div class="space-y-3">
            <div v-for="n in 3" :key="n" class="rounded-xl border border-border/30 p-4">
              <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-lg bg-muted/10" />
                <div class="flex-1 space-y-1">
                  <div class="h-4 w-40 bg-muted/10 rounded" />
                  <div class="h-3 w-20 bg-muted/10 rounded" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Product detail skeleton — matches 2-col hero + file info layout -->
  <div v-else-if="variant === 'product'">
    <div class="bg-gradient-to-br from-primary-50 via-surface to-success-50/30">
      <div class="container pt-8 pb-12 sm:pt-10 sm:pb-16">
        <div class="max-w-5xl mx-auto animate-pulse">
          <!-- Breadcrumb -->
          <div class="flex items-center gap-2 mb-6">
            <div class="h-3 w-14 bg-muted/10 rounded" />
            <div class="h-3 w-3 bg-muted/10 rounded" />
            <div class="h-3 w-12 bg-muted/10 rounded" />
            <div class="h-3 w-3 bg-muted/10 rounded" />
            <div class="h-3 w-20 bg-muted/10 rounded" />
          </div>
          <!-- 2-col hero -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Image (4:3 ratio like product) -->
            <div class="aspect-[4/3] rounded-2xl bg-muted/10" />
            <!-- Info -->
            <div class="space-y-4">
              <!-- Category -->
              <div class="h-6 w-24 bg-muted/10 rounded-full" />
              <!-- Title -->
              <div class="h-8 w-full bg-muted/10 rounded" />
              <div class="h-8 w-1/2 bg-muted/10 rounded" />
              <!-- Price -->
              <div class="h-10 w-32 bg-muted/10 rounded" />
              <!-- File info card (4 items) -->
              <div class="rounded-xl border border-border/30 p-5 space-y-3">
                <div class="h-3 w-24 bg-muted/10 rounded" />
                <div class="grid grid-cols-2 gap-4">
                  <div v-for="n in 4" :key="n" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-muted/10" />
                    <div class="space-y-1">
                      <div class="h-2.5 w-12 bg-muted/10 rounded" />
                      <div class="h-3.5 w-20 bg-muted/10 rounded" />
                    </div>
                  </div>
                </div>
              </div>
              <!-- CTA button -->
              <div class="h-12 w-full bg-muted/10 rounded-xl" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Description section -->
    <div class="bg-surface">
      <div class="container py-8 sm:py-12">
        <div class="max-w-5xl mx-auto animate-pulse">
          <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border/20">
            <div class="w-9 h-9 rounded-lg bg-muted/10" />
            <div class="h-6 w-36 bg-muted/10 rounded" />
          </div>
          <div class="space-y-4">
            <div class="h-4 w-full bg-muted/10 rounded" />
            <div class="h-4 w-full bg-muted/10 rounded" />
            <div class="h-4 w-5/6 bg-muted/10 rounded" />
            <div class="h-4 w-3/4 bg-muted/10 rounded" />
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Webinar detail skeleton — matches hero + 3-col body with sidebar -->
  <div v-else-if="variant === 'webinar'">
    <!-- Hero -->
    <div class="bg-gradient-to-br from-secondary-100/60 via-surface to-primary-100/40">
      <div class="container py-10 sm:py-14">
        <div class="animate-pulse">
          <!-- Breadcrumb -->
          <div class="flex items-center gap-2 mb-6">
            <div class="h-3 w-14 bg-muted/10 rounded" />
            <div class="h-3 w-3 bg-muted/10 rounded" />
            <div class="h-3 w-14 bg-muted/10 rounded" />
            <div class="h-3 w-3 bg-muted/10 rounded" />
            <div class="h-3 w-28 bg-muted/10 rounded" />
          </div>
          <!-- Content row -->
          <div class="flex flex-col md:flex-row gap-6 md:gap-8">
            <!-- Thumbnail -->
            <div class="w-full md:w-72 lg:w-80 shrink-0">
              <div class="aspect-video rounded-2xl bg-muted/10" />
            </div>
            <!-- Info -->
            <div class="flex-1 space-y-4">
              <div class="h-5 w-20 bg-muted/10 rounded-full" />
              <div class="h-8 w-full bg-muted/10 rounded" />
              <div class="h-8 w-2/3 bg-muted/10 rounded" />
              <!-- Schedule pills -->
              <div class="flex flex-wrap gap-3">
                <div class="h-8 w-36 bg-muted/10 rounded-lg" />
                <div class="h-8 w-28 bg-muted/10 rounded-lg" />
                <div class="h-8 w-24 bg-muted/10 rounded-lg" />
              </div>
              <!-- Price -->
              <div class="h-8 w-28 bg-muted/10 rounded" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Body: 3-col -->
    <div class="bg-surface">
      <div class="container py-10 sm:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12 animate-pulse">
          <!-- Main column -->
          <div class="lg:col-span-2 space-y-4">
            <div class="h-6 w-40 bg-muted/10 rounded" />
            <div class="h-4 w-full bg-muted/10 rounded" />
            <div class="h-4 w-full bg-muted/10 rounded" />
            <div class="h-4 w-5/6 bg-muted/10 rounded" />
            <div class="h-4 w-full bg-muted/10 rounded" />
            <div class="h-4 w-3/4 bg-muted/10 rounded" />
          </div>
          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Schedule card -->
            <div class="p-5 rounded-2xl border border-border/30 space-y-3">
              <div class="h-4 w-24 bg-muted/10 rounded" />
              <div class="h-3 w-full bg-muted/10 rounded" />
              <div class="h-3 w-full bg-muted/10 rounded" />
              <div class="h-3 w-3/4 bg-muted/10 rounded" />
            </div>
            <!-- Price card -->
            <div class="p-5 rounded-2xl border border-border/30 space-y-3">
              <div class="h-4 w-16 bg-muted/10 rounded" />
              <div class="h-8 w-28 bg-muted/10 rounded" />
              <div class="h-11 w-full bg-muted/10 rounded-xl" />
            </div>
            <!-- Mentor card -->
            <div class="hidden lg:block p-5 rounded-2xl border border-border/30 space-y-3">
              <div class="h-4 w-16 bg-muted/10 rounded" />
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-muted/10" />
                <div class="space-y-1.5 flex-1">
                  <div class="h-4 w-24 bg-muted/10 rounded" />
                  <div class="h-3 w-32 bg-muted/10 rounded" />
                </div>
              </div>
            </div>
          </div>
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
