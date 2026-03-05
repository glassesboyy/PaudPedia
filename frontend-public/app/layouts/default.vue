<script setup lang="ts">
/**
 * Default Layout
 *
 * Used for all public pages (home, courses, articles, etc.)
 * Provides header (via TheNavbar), main content area, and footer.
 */
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <TheNavbar show-nav />

    <main class="flex-1">
      <slot />
    </main>

    <!-- TODO: <AppFooter /> -->
    <footer class="border-t border-border bg-surface-muted">
      <div class="container py-6 text-center text-sm text-muted">
        &copy; {{ new Date().getFullYear() }} PaudPedia. All rights reserved.
      </div>
    </footer>
  </div>
</template>