<script setup lang="ts">
import { watch } from 'vue'
import BaseButton from '@/components/ui/Button/Button.vue'

interface Props {
  show: boolean
  title?: string
  message?: string
  confirmText?: string
  cancelText?: string
  variant?: 'danger' | 'warning' | 'primary'
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Konfirmasi',
  message: 'Apakah Anda yakin ingin melanjutkan?',
  confirmText: 'Ya, Lanjutkan',
  cancelText: 'Batal',
  variant: 'danger',
  loading: false,
})

const emit = defineEmits<{
  (e: 'confirm'): void
  (e: 'cancel'): void
}>()

// Lock body scroll when modal is open
watch(() => props.show, (val) => {
  if (val) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}, { immediate: true })

const variantConfig = {
  danger: {
    iconBg: 'bg-danger-100',
    iconColor: 'text-danger-600',
    icon: 'lucide:alert-triangle',
    buttonVariant: 'danger' as const,
  },
  warning: {
    iconBg: 'bg-amber-100',
    iconColor: 'text-amber-600',
    icon: 'lucide:alert-circle',
    buttonVariant: 'primary' as const,
  },
  primary: {
    iconBg: 'bg-primary-100',
    iconColor: 'text-primary-600',
    icon: 'lucide:help-circle',
    buttonVariant: 'primary' as const,
  },
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center">
        <!-- Backdrop -->
        <div 
          class="absolute inset-0 bg-black/50 backdrop-blur-sm"
          @click="!loading && emit('cancel')"
        />

        <!-- Modal -->
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden animate-modal-in">
          <div class="p-8 text-center space-y-5">
            <!-- Icon -->
            <div class="flex justify-center">
              <div 
                :class="[
                  'w-16 h-16 rounded-2xl flex items-center justify-center',
                  variantConfig[variant].iconBg
                ]"
              >
                <Icon 
                  :name="variantConfig[variant].icon" 
                  :class="['w-8 h-8', variantConfig[variant].iconColor]" 
                />
              </div>
            </div>

            <!-- Title -->
            <h3 class="text-xl font-black text-slate-900">{{ title }}</h3>

            <!-- Message -->
            <p class="text-sm text-slate-500 leading-relaxed max-w-sm mx-auto">{{ message }}</p>
          </div>

          <!-- Actions -->
          <div class="px-8 pb-8 flex gap-3">
            <BaseButton
              type="button"
              variant="outline"
              class="flex-1"
              :disabled="loading"
              @click="emit('cancel')"
            >
              {{ cancelText }}
            </BaseButton>
            <BaseButton
              type="button"
              :variant="variantConfig[variant].buttonVariant"
              class="flex-1"
              :loading="loading"
              @click="emit('confirm')"
            >
              {{ confirmText }}
            </BaseButton>
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
</style>
