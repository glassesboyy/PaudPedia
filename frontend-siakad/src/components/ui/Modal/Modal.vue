<script setup lang="ts">
import { watch } from 'vue'

interface Props {
  show: boolean
  title?: string
  maxWidth?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: '',
  maxWidth: 'max-w-xl',
})

const emit = defineEmits<{
  (e: 'close'): void
}>()

// Lock body scroll when modal is open
watch(() => props.show, (val) => {
  if (val) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}, { immediate: true })
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div 
          class="absolute inset-0 bg-black/50 backdrop-blur-sm"
          @click="emit('close')"
        />

        <!-- Modal -->
        <div :class="['relative bg-white rounded-3xl shadow-2xl w-full overflow-hidden animate-modal-in flex flex-col max-h-[90vh]', maxWidth]">
          <!-- Header -->
          <div class="px-6 py-4 border-b border-border flex items-center justify-between shrink-0 bg-surface-muted/30">
            <h3 class="text-lg font-bold text-heading">{{ title }}</h3>
            <button @click="emit('close')" class="p-1.5 rounded-lg text-muted hover:text-slate-700 hover:bg-slate-100 transition-colors">
              <Icon name="lucide:x" class="w-5 h-5" />
            </button>
          </div>

          <!-- Body -->
          <div class="p-6 overflow-y-auto custom-scrollbar">
            <slot />
          </div>

          <!-- Footer -->
          <div v-if="$slots.footer" class="px-6 py-4 border-t border-border bg-surface-muted/30 shrink-0">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@keyframes modal-in {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-modal-in {
  animation: modal-in 0.25s ease-out;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
</style>
