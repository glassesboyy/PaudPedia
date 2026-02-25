<script setup lang="ts">
/**
 * Minimal Layout
 *
 * Used for checkout and other pages that need minimal distraction.
 */
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <header class="border-b border-border bg-surface">
      <div class="container py-4 flex items-center justify-between">
        <NuxtLink to="/" class="font-bold text-xl text-primary-600">
          PaudPedia
        </NuxtLink>
      </div>
    </header>

    <main class="flex-1">
      <slot />
    </main>
  </div>
</template>
