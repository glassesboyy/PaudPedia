<script setup lang="ts">
/**
 * BaseAlert — Feedback alert component.
 *
 * Usage:
 *   <BaseAlert variant="danger" dismissible @dismiss="clearError">
 *     Login gagal. Periksa email dan password Anda.
 *   </BaseAlert>
 */

export interface AlertProps {
  variant?: 'info' | 'success' | 'warning' | 'danger'
  dismissible?: boolean
}

withDefaults(defineProps<AlertProps>(), {
  variant: 'info',
  dismissible: false,
})

const emit = defineEmits<{
  dismiss: []
}>()

const variantClasses: Record<string, string> = {
  info:    'bg-primary-50 border-primary-200 text-primary-800',
  success: 'bg-success-50 border-success-200 text-success-800',
  warning: 'bg-warning-50 border-warning-200 text-warning-800',
  danger:  'bg-danger-50 border-danger-200 text-danger-800',
}

const iconPaths: Record<string, string> = {
  info:    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
  warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
  danger:  'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
}
</script>

<template>
  <div
    :class="[
      'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm animate-fade-in',
      variantClasses[variant],
    ]"
    role="alert"
  >
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" :d="iconPaths[variant]" />
    </svg>

    <div class="flex-1 min-w-0">
      <slot />
    </div>

    <button
      v-if="dismissible"
      class="flex-shrink-0 -mr-1 -mt-1 p-1 rounded hover:bg-black/5 transition-colors"
      @click="emit('dismiss')"
    >
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>
