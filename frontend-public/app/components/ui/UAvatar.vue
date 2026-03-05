<script setup lang="ts">
/**
 * UAvatar — Avatar display atom with image or initials fallback.
 *
 * Uses design system tokens exclusively.
 */
interface Props {
  src?: string | null
  name?: string
  size?: 'sm' | 'md' | 'lg' | 'xl'
  rounded?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  src: null,
  name: '',
  size: 'md',
  rounded: true,
})

const sizeClasses: Record<string, string> = {
  sm: 'w-8 h-8 text-xs',
  md: 'w-10 h-10 text-sm',
  lg: 'w-16 h-16 text-lg',
  xl: 'w-24 h-24 text-2xl',
}

const initials = computed(() => {
  if (!props.name) return '?'
  const parts = props.name.trim().split(/\s+/)
  if (parts.length >= 2) {
    return ((parts[0]?.[0] ?? '') + (parts[1]?.[0] ?? '')).toUpperCase()
  }
  return (parts[0] ?? '').substring(0, 2).toUpperCase()
})

const imgError = ref(false)
const showImage = computed(() => props.src && !imgError.value)

function onImgError() {
  imgError.value = true
}

// Reset error state when src changes
watch(() => props.src, () => {
  imgError.value = false
})
</script>

<template>
  <div
    :class="[
      'inline-flex items-center justify-center shrink-0 overflow-hidden',
      'bg-primary-100 text-primary-600 font-semibold',
      'border border-border shadow-soft',
      sizeClasses[size],
      rounded ? 'rounded-full' : 'rounded-lg',
    ]"
  >
    <img
      v-if="showImage"
      :src="src!"
      :alt="name || 'Avatar'"
      class="w-full h-full object-cover"
      @error="onImgError"
    />
    <span v-else>{{ initials }}</span>
  </div>
</template>
