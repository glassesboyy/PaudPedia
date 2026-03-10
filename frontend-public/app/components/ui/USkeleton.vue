<script setup lang="ts">
/**
 * USkeleton — Base skeleton placeholder with animated pulse.
 *
 * @prop variant - Shape: 'text' | 'circular' | 'rectangular' | 'rounded' (default: 'text')
 * @prop width   - CSS width (e.g. '100%', '200px', '3/4'). Tailwind fraction shorthand supported.
 * @prop height  - CSS height (e.g. '16px', '1rem'). For text variant, defaults to '1em'.
 * @prop rounded - Border radius override (e.g. 'full', '2xl', 'lg'). Auto-set by variant.
 *
 * Usage:
 *   <USkeleton />                                    — single text line
 *   <USkeleton variant="circular" width="40px" height="40px" />  — avatar
 *   <USkeleton variant="rectangular" height="200px" />           — image
 *   <USkeleton variant="rounded" width="80px" height="24px" />   — badge/pill
 */

interface Props {
  variant?: 'text' | 'circular' | 'rectangular' | 'rounded'
  width?: string
  height?: string
  rounded?: string
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'text',
  width: undefined,
  height: undefined,
  rounded: undefined,
})

const defaultRounded: Record<string, string> = {
  text: 'rounded',
  circular: 'rounded-full',
  rectangular: 'rounded-lg',
  rounded: 'rounded-2xl',
}

const radiusClass = computed(() => {
  if (props.rounded) return `rounded-${props.rounded}`
  return defaultRounded[props.variant]
})

const style = computed(() => {
  const s: Record<string, string> = {}

  // Width
  if (props.width) {
    s.width = props.width
  } else if (props.variant === 'text') {
    s.width = '100%'
  }

  // Height
  if (props.height) {
    s.height = props.height
  } else if (props.variant === 'text') {
    s.height = '1em'
  } else if (props.variant === 'circular') {
    // Circular defaults to square if only width given
    s.height = props.width || '40px'
    if (!props.width) s.width = '40px'
  }

  return s
})
</script>

<template>
  <div
    class="animate-pulse bg-muted/10"
    :class="radiusClass"
    :style="style"
  />
</template>
