<script setup lang="ts">
/**
 * ToastContainer — Renders toast notifications from the UI store.
 *
 * Place once in app.vue. Toasts appear at the top-right corner.
 */
import { useUIStore } from '~~/stores/ui';

const uiStore = useUIStore()

const variantConfig = {
  success: { bg: 'bg-success-50', border: 'border-success-200', text: 'text-success-800', icon: 'lucide:check-circle' },
  error:   { bg: 'bg-danger-50',  border: 'border-danger-200',  text: 'text-danger-800',  icon: 'lucide:alert-circle' },
  warning: { bg: 'bg-warning-50', border: 'border-warning-200', text: 'text-warning-800', icon: 'lucide:alert-triangle' },
  info:    { bg: 'bg-primary-50', border: 'border-primary-200', text: 'text-primary-800', icon: 'lucide:info' },
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="uiStore.toasts.length"
      class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-full max-w-sm"
      aria-live="polite"
    >
      <TransitionGroup
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
      >
        <div
          v-for="toast in uiStore.toasts"
          :key="toast.id"
          role="alert"
          :class="[
            'rounded-lg border p-4 text-sm shadow-lg',
            variantConfig[toast.type].bg,
            variantConfig[toast.type].border,
            variantConfig[toast.type].text,
          ]"
        >
          <div class="flex items-start gap-3">
            <Icon :name="variantConfig[toast.type].icon" class="w-5 h-5 shrink-0 mt-0.5" />
            <p class="flex-1 min-w-0">{{ toast.message }}</p>
            <button
              type="button"
              class="shrink-0 -mr-1 opacity-70 hover:opacity-100 transition-opacity"
              @click="uiStore.removeToast(toast.id)"
            >
              <Icon name="lucide:x" class="w-4 h-4" />
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>
