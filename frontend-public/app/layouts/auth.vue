<script setup lang="ts">
/**
 * Auth Layout
 *
 * Minimal centered layout for login, register, forgot password, etc.
 */
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-surface-muted px-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <NuxtLink to="/" class="text-2xl font-bold text-primary-600">
          PaudPedia
        </NuxtLink>
      </div>

      <div class="bg-surface rounded-xl shadow-card border border-border p-6">
        <slot />
      </div>
    </div>
  </div>
</template>
