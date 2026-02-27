<script setup lang="ts">
/**
 * UInput — Text input atom with label, error, and password toggle.
 *
 * Uses design system tokens exclusively.
 */
interface Props {
  label?: string
  type?: string
  placeholder?: string
  error?: string
  required?: boolean
  disabled?: boolean
  autocomplete?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  required: false,
  disabled: false,
})

defineEmits<{ 'clear-error': [] }>()

const model = defineModel<string>({ default: '' })

const inputId = useId()
const showPassword = ref(false)

const currentType = computed(() => {
  if (props.type === 'password') return showPassword.value ? 'text' : 'password'
  return props.type
})
</script>

<template>
  <div>
    <label
      v-if="label"
      :for="inputId"
      class="block text-sm font-medium text-foreground mb-1.5"
    >
      {{ label }}
      <span v-if="required" class="text-danger-500">*</span>
    </label>

    <div class="relative">
      <input
        :id="inputId"
        v-model="model"
        :type="currentType"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :autocomplete="autocomplete"
        :class="[
          'block w-full rounded-lg border px-3 py-2.5 text-sm',
          'text-foreground bg-surface placeholder:text-muted',
          'transition-colors duration-200',
          'focus:outline-none focus:ring-2 focus:ring-offset-0',
          'disabled:opacity-50 disabled:cursor-not-allowed',
          error
            ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/30'
            : 'border-border focus:border-primary-500 focus:ring-primary-500/30',
        ]"
        @input="$emit('clear-error')"
      />

      <!-- password visibility toggle -->
      <button
        v-if="type === 'password'"
        type="button"
        tabindex="-1"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-foreground transition-colors"
        @click="showPassword = !showPassword"
      >
        <Icon v-if="showPassword" name="lucide:eye-off" class="w-4 h-4" />
        <Icon v-else name="lucide:eye" class="w-4 h-4" />
      </button>
    </div>

    <p v-if="error" class="mt-1.5 text-sm text-danger-600">{{ error }}</p>
  </div>
</template>
