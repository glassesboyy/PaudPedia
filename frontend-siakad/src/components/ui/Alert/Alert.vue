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

const iconNames: Record<string, string> = {
  info:    'lucide:info',
  success: 'lucide:check-circle-2',
  warning: 'lucide:alert-triangle',
  danger:  'lucide:x-circle',
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
    <Icon :name="iconNames[variant]" class="w-5 h-5 flex-shrink-0 mt-0.5" />

    <div class="flex-1 min-w-0">
      <slot />
    </div>

    <button
      v-if="dismissible"
      class="flex-shrink-0 -mr-1 -mt-1 p-1 rounded hover:bg-black/5 transition-colors focus:outline-none"
      @click="emit('dismiss')"
    >
      <Icon name="lucide:x" class="w-4 h-4" />
    </button>
  </div>
</template>
