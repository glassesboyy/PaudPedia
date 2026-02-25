<script setup lang="ts">
/**
 * Dashboard Layout
 *
 * Layout for authenticated user account pages.
 * Provides a sidebar + main content area.
 */
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- TODO: <AppHeader /> -->
    <header class="border-b border-border bg-surface">
      <div class="container py-4">
        <span class="font-bold text-xl text-primary-600">PaudPedia</span>
      </div>
    </header>

    <div class="flex-1 container py-8">
      <div class="flex gap-8">
        <!-- TODO: Sidebar navigation component -->
        <aside class="hidden lg:block w-64 shrink-0">
          <nav class="space-y-1">
            <!-- Sidebar links will be implemented in feature phase -->
          </nav>
        </aside>

        <main class="flex-1 min-w-0">
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>
