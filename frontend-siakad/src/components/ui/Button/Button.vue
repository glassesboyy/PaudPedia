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
    'bg-primary-600 text-white hover:bg-primary-700 active:scale-95 shadow-sm',
  secondary:
    'bg-white text-slate-800 border border-slate-200 hover:bg-slate-50 active:scale-95 shadow-sm',
  outline:
    'border border-primary-600 bg-transparent text-primary-600 hover:bg-primary-50 active:scale-95',
  ghost:
    'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 active:scale-95',
  danger:
    'bg-red-600 text-white hover:bg-red-700 active:scale-95 shadow-sm',
}

const sizeClasses: Record<string, string> = {
  sm: 'px-3.5 py-1.5 text-xs rounded-lg gap-1.5',
  md: 'px-5 py-2.5 text-sm rounded-xl gap-2 font-bold',
  lg: 'px-7 py-3.5 text-base rounded-xl gap-2.5 font-bold',
}
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center transition-all duration-200 ease-smooth',
      'focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary-500/10',
      'disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none',
      variantClasses[variant],
      sizeClasses[size],
      block ? 'w-full' : '',
    ]"
    @click="$emit('click', $event)"
  >
    <!-- Loading spinner -->
    <Icon
      v-if="loading"
      name="lucide:loader-2"
      class="animate-spin h-4 w-4 shrink-0"
    />

    <!-- Prepend slot -->
    <slot name="prepend" v-if="!loading" />

    <!-- Content -->
    <slot />

    <!-- Append slot -->
    <slot name="append" />
  </button>
</template>
