<script setup lang="ts">
/**
 * ULoading — Reusable loading spinner component.
 *
 * @prop size    - Spinner size: 'xs' | 'sm' | 'md' | 'lg' | 'xl' (default: 'md')
 * @prop text    - Optional loading text displayed below spinner
 * @prop overlay - If true, renders as fullscreen overlay with backdrop
 * @prop inline  - If true, renders inline (no centering wrapper)
 *
 * Usage:
 *   <ULoading />
 *   <ULoading size="lg" text="Memuat halaman..." />
 *   <ULoading overlay />
 *   <ULoading inline size="sm" />
 */

interface Props {
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  text?: string
  overlay?: boolean
  inline?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  text: undefined,
  overlay: false,
  inline: false,
})

const sizeClasses: Record<string, string> = {
  xs: 'h-3.5 w-3.5',
  sm: 'h-5 w-5',
  md: 'h-8 w-8',
  lg: 'h-10 w-10',
  xl: 'h-14 w-14',
}

const textSizeClasses: Record<string, string> = {
  xs: 'text-[10px]',
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-base',
  xl: 'text-lg',
}

const spinnerSize = computed(() => sizeClasses[props.size])
const textSize = computed(() => textSizeClasses[props.size])
</script>

<template>
  <!-- Overlay mode -->
  <Teleport v-if="overlay" to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div class="text-center">
        <svg
          class="animate-spin text-white mx-auto"
          :class="spinnerSize"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
        <p v-if="text" class="mt-3 text-white/90" :class="textSize">{{ text }}</p>
      </div>
    </div>
  </Teleport>

  <!-- Inline mode (inherits parent text color) -->
  <span v-else-if="inline" class="inline-flex items-center gap-2">
    <svg
      class="animate-spin"
      :class="spinnerSize"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
    </svg>
    <span v-if="text" class="text-muted" :class="textSize">{{ text }}</span>
  </span>

  <!-- Default: centered block -->
  <div v-else class="flex items-center justify-center">
    <div class="text-center">
      <svg
        class="animate-spin text-primary-500 mx-auto"
        :class="spinnerSize"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
      </svg>
      <p v-if="text" class="mt-3 text-muted" :class="textSize">{{ text }}</p>
    </div>
  </div>
</template>
