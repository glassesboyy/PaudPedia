<script setup lang="ts">
/**
 * BaseInput — Form input component with label, error, and icon support.
 *
 * Usage:
 *   <BaseInput
 *     v-model="form.email"
 *     label="Email"
 *     type="email"
 *     placeholder="nama@email.com"
 *     :error="errors.email"
 *   />
 */

export interface InputProps {
  modelValue?: string | number
  label?: string
  type?: string
  placeholder?: string
  error?: string
  disabled?: boolean
  required?: boolean
  id?: string
  minlength?: number
  maxlength?: number
  pattern?: string
}

const props = withDefaults(defineProps<InputProps>(), {
  modelValue: '',
  type: 'text',
  placeholder: '',
  error: '',
  disabled: false,
  required: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

const inputId = props.id || `input-${Math.random().toString(36).slice(2, 9)}`

function onInput(event: Event) {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}
</script>

<template>
  <div class="w-full">
    <label v-if="label" :for="inputId" class="label">
      {{ label }}
      <span v-if="required" class="text-danger-500 ml-0.5">*</span>
    </label>

    <div class="relative">
      <!-- Prepend icon slot -->
      <div v-if="$slots.prepend" class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted">
        <slot name="prepend" />
      </div>

      <input
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :minlength="minlength"
        :maxlength="maxlength"
        :pattern="pattern"
        :class="[
          'input-field',
          error ? 'input-error' : '',
          $slots.prepend ? 'pl-10' : '',
          $slots.append ? 'pr-10' : '',
        ]"
        @input="onInput"
      />

      <!-- Append icon slot -->
      <div v-if="$slots.append" class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted">
        <slot name="append" />
      </div>
    </div>

    <p v-if="error" class="mt-1.5 text-xs text-danger-600">
      {{ error }}
    </p>
  </div>
</template>
