<script setup lang="ts">
/**
 * UAlert — Feedback message atom.
 *
 * Variants: success · error · warning · info
 */
interface Props {
  variant?: 'success' | 'error' | 'warning' | 'info'
  title?: string
  dismissible?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'info',
  dismissible: false,
})

const isVisible = ref(true)

const variantConfig = {
  success: { bg: 'bg-success-50', border: 'border-success-200', text: 'text-success-800', icon: 'lucide:check-circle' },
  error:   { bg: 'bg-danger-50',  border: 'border-danger-200',  text: 'text-danger-800',  icon: 'lucide:alert-circle' },
  warning: { bg: 'bg-warning-50', border: 'border-warning-200', text: 'text-warning-800', icon: 'lucide:alert-triangle' },
  info:    { bg: 'bg-primary-50', border: 'border-primary-200', text: 'text-primary-800', icon: 'lucide:info' },
}

const config = computed(() => variantConfig[props.variant])
</script>

<template>
  <div
    v-if="isVisible"
    role="alert"
    :class="['rounded-lg border p-4 text-sm animate-fade-in', config.bg, config.border, config.text]"
  >
    <div class="flex items-start gap-3">
      <Icon :name="config.icon" class="w-5 h-5 shrink-0 mt-0.5" />

      <div class="flex-1 min-w-0">
        <p v-if="title" class="font-medium mb-1">{{ title }}</p>
        <div><slot /></div>
      </div>

      <button
        v-if="dismissible"
        type="button"
        class="shrink-0 -mr-1 opacity-70 hover:opacity-100 transition-opacity"
        @click="isVisible = false"
      >
        <Icon name="lucide:x" class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>
