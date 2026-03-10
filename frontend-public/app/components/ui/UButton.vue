<script setup lang="ts">
/**
 * UButton — Primary action button atom.
 *
 * Supports variants, sizes, loading/disabled states.
 * Uses design system tokens exclusively.
 */
interface Props {
  type?: 'button' | 'submit' | 'reset'
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost' | 'outline'
  size?: 'sm' | 'md' | 'lg'
  loading?: boolean
  disabled?: boolean
  block?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'button',
  variant: 'primary',
  size: 'md',
  loading: false,
  disabled: false,
  block: false,
})

const isDisabled = computed(() => props.disabled || props.loading)

const variantClasses: Record<string, string> = {
  primary:
    'bg-primary-600 text-on-primary hover:bg-primary-700 focus:ring-primary-500/50',
  secondary:
    'bg-secondary-500 text-on-primary hover:bg-secondary-600 focus:ring-secondary-500/50',
  danger:
    'bg-danger-600 text-on-primary hover:bg-danger-700 focus:ring-danger-500/50',
  ghost:
    'bg-transparent text-foreground hover:bg-surface-sunken focus:ring-primary-500/50',
  outline:
    'border border-border bg-transparent text-foreground hover:bg-surface-muted focus:ring-primary-500/50',
}

const sizeClasses: Record<string, string> = {
  sm: 'px-3 py-1.5 text-sm',
  md: 'px-4 py-2.5 text-sm',
  lg: 'px-6 py-3 text-base',
}
</script>

<template>
  <button
    :type="type"
    :disabled="isDisabled"
    :class="[
      'inline-flex items-center justify-center font-medium rounded-lg',
      'transition-colors duration-200',
      'focus:outline-none focus:ring-2 focus:ring-offset-2',
      'disabled:opacity-50 disabled:cursor-not-allowed',
      variantClasses[variant],
      sizeClasses[size],
      block && 'w-full',
    ]"
  >
    <!-- spinner -->
    <ULoading v-if="loading" inline size="xs" class="-ml-1 mr-1.5" />
    <slot />
  </button>
</template>
