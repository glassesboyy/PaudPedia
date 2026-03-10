<script setup lang="ts">
/**
 * SkeletonCardGrid — Grid of skeleton cards for list pages.
 *
 * Replaces the repeated card-grid skeleton pattern across:
 * courses, webinars, products, articles, mentors list pages.
 *
 * @prop count   - Number of skeleton cards (default: 6)
 * @prop columns - Grid columns: 2 | 3 | 4 (default: 3)
 * @prop variant - Card type: 'media' | 'profile' | 'article' | 'simple' (default: 'media')
 *
 * Variants:
 * - media:   thumbnail (aspect-video) + 3 text lines (courses, webinars, products)
 * - profile: centered avatar + name + subtitle + tags + stats (mentors)
 * - article: thumbnail (16/10) + tag pills + title + desc + author row (articles)
 * - simple:  icon + 2 text lines (contact cards)
 */

interface Props {
  count?: number
  columns?: 2 | 3 | 4
  variant?: 'media' | 'profile' | 'article' | 'simple'
}

const props = withDefaults(defineProps<Props>(), {
  count: 6,
  columns: 3,
  variant: 'media',
})

const gridClass = computed(() => {
  const cols: Record<number, string> = {
    2: 'grid-cols-1 sm:grid-cols-2',
    3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
  }
  return cols[props.columns]
})
</script>

<template>
  <div class="grid gap-6" :class="gridClass">
    <div
      v-for="i in count"
      :key="i"
      class="animate-pulse rounded-2xl border border-border bg-surface"
      :class="variant === 'media' || variant === 'article' ? 'overflow-hidden' : 'p-6'"
    >
      <!-- Media variant (courses, webinars, products) -->
      <template v-if="variant === 'media'">
        <div class="aspect-video bg-muted/10" />
        <div class="p-4 space-y-3">
          <div class="h-3 w-16 bg-muted/10 rounded" />
          <div class="h-4 w-3/4 bg-muted/10 rounded" />
          <div class="h-3 w-1/2 bg-muted/10 rounded" />
        </div>
      </template>

      <!-- Profile variant (mentors) -->
      <template v-else-if="variant === 'profile'">
        <div class="w-20 h-20 rounded-2xl bg-muted/10 mx-auto mb-4" />
        <div class="h-4 w-2/3 bg-muted/10 rounded mx-auto mb-2" />
        <div class="h-3 w-1/2 bg-muted/10 rounded mx-auto mb-3" />
        <div class="flex justify-center gap-1.5 mb-3">
          <div class="h-5 w-16 bg-muted/10 rounded-full" />
          <div class="h-5 w-14 bg-muted/10 rounded-full" />
        </div>
        <div class="h-3 w-full bg-muted/10 rounded mb-1" />
        <div class="h-3 w-3/4 bg-muted/10 rounded mx-auto mb-3" />
        <div class="flex justify-center gap-4">
          <div class="h-3 w-16 bg-muted/10 rounded" />
          <div class="h-3 w-16 bg-muted/10 rounded" />
        </div>
      </template>

      <!-- Article variant -->
      <template v-else-if="variant === 'article'">
        <div class="aspect-[16/10] bg-muted/10" />
        <div class="p-5 space-y-3">
          <div class="flex gap-1.5">
            <div class="h-4 w-14 bg-muted/10 rounded-full" />
            <div class="h-4 w-12 bg-muted/10 rounded-full" />
          </div>
          <div class="h-5 w-3/4 bg-muted/10 rounded" />
          <div class="h-4 w-full bg-muted/10 rounded" />
          <div class="h-4 w-2/3 bg-muted/10 rounded" />
          <div class="pt-3 border-t border-border/40 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 rounded-full bg-muted/10" />
              <div class="h-3 w-20 bg-muted/10 rounded" />
            </div>
            <div class="h-3 w-12 bg-muted/10 rounded" />
          </div>
        </div>
      </template>

      <!-- Simple variant (contact cards) -->
      <template v-else>
        <div class="w-11 h-11 rounded-xl bg-muted/10 mb-3" />
        <div class="h-3 w-16 bg-muted/10 rounded mb-2" />
        <div class="h-4 w-28 bg-muted/10 rounded" />
      </template>
    </div>
  </div>
</template>
