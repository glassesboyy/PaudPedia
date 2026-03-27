<script setup lang="ts">
/**
 * BaseCard — Card container component with optional header/footer.
 *
 * Usage:
 *   <BaseCard>
 *     <template #header>Title</template>
 *     Card content here
 *     <template #footer>Actions</template>
 *   </BaseCard>
 */

export interface CardProps {
  variant?: 'default' | 'bordered' | 'elevated'
  noPadding?: boolean
}

withDefaults(defineProps<CardProps>(), {
  variant: 'default',
  noPadding: false,
})

const variantClasses: Record<string, string> = {
  default:  'card',
  bordered: 'card border-2',
  elevated: 'bg-surface rounded-xl shadow-medium border-0',
}
</script>

<template>
  <div :class="variantClasses[variant]">
    <div v-if="$slots.header" class="card-header">
      <slot name="header" />
    </div>

    <div :class="noPadding ? '' : 'card-body'">
      <slot />
    </div>

    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>
