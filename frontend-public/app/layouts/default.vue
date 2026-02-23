<script setup lang="ts">
/**
 * Default Layout
 *
 * Used for all public pages (home, courses, articles, etc.)
 * Provides header, main content area, and footer.
 */
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- TODO: <AppHeader /> -->
    <header class="border-b bg-white">
      <div class="container mx-auto px-4 py-4">
        <span class="font-bold text-xl text-primary-600">PaudPedia</span>
      </div>
    </header>

    <main class="flex-1">
      <slot />
    </main>

    <!-- TODO: <AppFooter /> -->
    <footer class="border-t bg-gray-50">
      <div class="container mx-auto px-4 py-6 text-center text-sm text-gray-500">
        &copy; {{ new Date().getFullYear() }} PaudPedia. All rights reserved.
      </div>
    </footer>
  </div>
</template>
