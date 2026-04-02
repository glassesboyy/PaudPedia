<script setup lang="ts">
/**
 * BaseSkeleton — Reusable loading state component.
 * Supports different shapes (text, circle, rect) and sizes.
 */

interface SkeletonProps {
  variant?: 'text' | 'rect' | 'circle'
  width?: string
  height?: string
  class?: string
}

withDefaults(defineProps<SkeletonProps>(), {
  variant: 'rect',
  width: '100%',
})
</script>

<template>
  <div
    :class="[
      'animate-pulse bg-muted/40',
      variant === 'text' ? 'h-3 rounded' : '',
      variant === 'circle' ? 'rounded-full' : 'rounded-lg',
      $props.class
    ]"
    :style="{ 
      width: width, 
      height: height 
    }"
  ></div>
</template>
