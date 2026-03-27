<script setup lang="ts">
/**
 * BaseButton — Primary interactive button component.
 *
 * Variants: primary, secondary, outline, ghost, danger
 * Sizes:    sm, md, lg
 *
 * Usage:
 *   <BaseButton variant="primary" size="md" :loading="isLoading" @click="doSomething">
 *     Save Changes
 *   </BaseButton>
 */

export interface ButtonProps {
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger'
  size?: 'sm' | 'md' | 'lg'
  type?: 'button' | 'submit' | 'reset'
  loading?: boolean
  disabled?: boolean
  block?: boolean
}

withDefaults(defineProps<ButtonProps>(), {
  variant: 'primary',
  size: 'md',
  type: 'button',
  loading: false,
  disabled: false,
  block: false,
})

defineEmits<{
  click: [event: MouseEvent]
}>()

const variantClasses: Record<string, string> = {
  primary:
    'bg-primary-600 text-white hover:bg-primary-700 active:bg-primary-800 shadow-sm',
  secondary:
    'bg-secondary-500 text-white hover:bg-secondary-600 active:bg-secondary-700 shadow-sm',
  outline:
    'border border-border bg-transparent text-heading hover:bg-surface-muted active:bg-surface-sunken',
  ghost:
    'bg-transparent text-heading hover:bg-surface-muted active:bg-surface-sunken',
  danger:
    'bg-danger-600 text-white hover:bg-danger-700 active:bg-danger-800 shadow-sm',
}

const sizeClasses: Record<string, string> = {
  sm: 'px-3 py-1.5 text-xs rounded-md gap-1.5',
  md: 'px-4 py-2.5 text-sm rounded-lg gap-2',
  lg: 'px-6 py-3 text-base rounded-lg gap-2.5',
}
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center font-medium transition-all duration-200 ease-smooth',
      'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
      'disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none',
      variantClasses[variant],
      sizeClasses[size],
      block ? 'w-full' : '',
    ]"
    @click="$emit('click', $event)"
  >
    <!-- Loading spinner -->
    <svg
      v-if="loading"
      class="animate-spin -ml-0.5 h-4 w-4"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>

    <slot />
  </button>
</template>
