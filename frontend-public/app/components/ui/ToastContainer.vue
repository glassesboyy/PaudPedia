<script setup lang="ts">
/**
 * ToastContainer — Renders toast notifications from the UI store.
 *
 * Place once in app.vue. Toasts appear at the top-right corner.
 * Styled with a friendly, playful feel suitable for a PAUD website
 * while staying within the design system tokens.
 */
import { useUIStore } from '~~/stores/ui';

const uiStore = useUIStore()

const variantConfig = {
  success: {
    bg: 'bg-success-50',
    border: 'border-success-200',
    text: 'text-success-800',
    icon: 'lucide:check-circle',
    accentBorder: 'border-l-success-500',
  },
  error: {
    bg: 'bg-danger-50',
    border: 'border-danger-200',
    text: 'text-danger-800',
    icon: 'lucide:alert-circle',
    accentBorder: 'border-l-danger-500',
  },
  warning: {
    bg: 'bg-warning-50',
    border: 'border-warning-200',
    text: 'text-warning-800',
    icon: 'lucide:alert-triangle',
    accentBorder: 'border-l-warning-500',
  },
  info: {
    bg: 'bg-primary-50',
    border: 'border-primary-200',
    text: 'text-primary-800',
    icon: 'lucide:info',
    accentBorder: 'border-l-primary-500',
  },
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
        enter-active-class="toast-enter-active"
        enter-from-class="toast-enter-from"
        enter-to-class="toast-enter-to"
        leave-active-class="toast-leave-active"
        leave-from-class="toast-leave-from"
        leave-to-class="toast-leave-to"
      >
        <div
          v-for="toast in uiStore.toasts"
          :key="toast.id"
          role="alert"
          :class="[
            'rounded-2xl border border-l-4 px-4 py-3.5 text-sm shadow-card',
            variantConfig[toast.type].bg,
            variantConfig[toast.type].border,
            variantConfig[toast.type].text,
            variantConfig[toast.type].accentBorder,
          ]"
        >
          <div class="flex items-center gap-3">
            <div class="flex-1 min-w-0">
              <p class="font-medium leading-snug">{{ toast.message }}</p>
            </div>
            <button
              type="button"
              class="shrink-0 -mr-1 p-1 rounded-full opacity-60 hover:opacity-100 hover:bg-black/5 transition-all duration-200"
              @click="uiStore.removeToast(toast.id)"
            >
              <Icon name="lucide:x" class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
/* Enter animation: slide in from right with a subtle bounce */
.toast-enter-active {
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}
.toast-enter-to {
  opacity: 1;
  transform: translateX(0) scale(1);
}

/* Leave animation: fade out smoothly */
.toast-leave-active {
  transition: all 0.25s ease-in;
}
.toast-leave-from {
  opacity: 1;
  transform: translateX(0) scale(1);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.95);
}
</style>
