<script setup lang="ts">
/**
 * BaseSelect — Reusable select component consistent with BaseInput styling.
 */

export interface SelectProps {
  modelValue?: string | number
  label?: string
  options: { value: string | number; label: string; disabled?: boolean }[]
  placeholder?: string
  error?: string
  disabled?: boolean
  required?: boolean
  id?: string
}

const props = withDefaults(defineProps<SelectProps>(), {
  modelValue: '',
  placeholder: 'Pilih opsi...',
  error: '',
  disabled: false,
  required: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

const selectId = props.id || `select-${Math.random().toString(36).slice(2, 9)}`

function onChange(event: Event) {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', target.value)
}
</script>

<template>
  <div class="w-full">
    <label v-if="label" :for="selectId" class="label">
      {{ label }}
      <span v-if="required" class="text-danger-500 ml-0.5">*</span>
    </label>

    <div class="relative">
      <!-- Prepend icon slot -->
      <div v-if="$slots.prepend" class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted">
        <slot name="prepend" />
      </div>

      <select
        :id="selectId"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :class="[
          'input-field appearance-none',
          error ? 'input-error' : '',
          $slots.prepend ? 'pl-10' : '',
        ]"
        @change="onChange"
      >
        <option value="" disabled selected>{{ placeholder }}</option>
        <option 
          v-for="opt in options" 
          :key="opt.value" 
          :value="opt.value"
          :disabled="opt.disabled"
        >
          {{ opt.label }}
        </option>
      </select>

      <!-- Custom Chevron Icon -->
      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted">
        <Icon name="lucide:chevron-down" class="w-4 h-4" />
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-danger-600">
      {{ error }}
    </p>
  </div>
</template>
