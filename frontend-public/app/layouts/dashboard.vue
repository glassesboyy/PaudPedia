<script setup lang="ts">
/**
 * Dashboard Layout
 *
 * Layout for authenticated user account pages.
 * Provides a sidebar + main content area.
 */
const uiStore = useUIStore()
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="border-b border-border bg-surface sticky top-0 z-dropdown">
      <div class="container py-4 flex items-center justify-between">
        <NuxtLink to="/" class="font-bold text-xl text-primary-600">PaudPedia</NuxtLink>
        <!-- Mobile sidebar toggle -->
        <button
          type="button"
          class="lg:hidden p-2 rounded-lg text-body hover:bg-surface-muted transition-colors"
          @click="uiStore.toggleMobileSidebar()"
        >
          <Icon name="lucide:menu" class="w-5 h-5" />
        </button>
      </div>
    </header>

    <div class="flex-1 container py-8">
      <div class="flex gap-8">
        <!-- Desktop sidebar -->
        <aside class="hidden lg:block w-64 shrink-0">
          <div class="sticky top-24">
            <AccountSidebar />
          </div>
        </aside>

        <!-- Mobile sidebar overlay -->
        <Teleport to="body">
          <Transition name="fade">
            <div
              v-if="uiStore.isMobileSidebarOpen"
              class="fixed inset-0 bg-black/40 z-dropdown lg:hidden"
              @click="uiStore.toggleMobileSidebar()"
            />
          </Transition>
          <Transition name="slide-sidebar">
            <aside
              v-if="uiStore.isMobileSidebarOpen"
              class="fixed inset-y-0 left-0 w-72 bg-surface border-r border-border z-dropdown p-4 overflow-y-auto lg:hidden"
            >
              <div class="flex items-center justify-between mb-4">
                <span class="font-bold text-lg text-primary-600">Menu</span>
                <button
                  type="button"
                  class="p-2 rounded-lg text-body hover:bg-surface-muted transition-colors"
                  @click="uiStore.toggleMobileSidebar()"
                >
                  <Icon name="lucide:x" class="w-5 h-5" />
                </button>
              </div>
              <AccountSidebar />
            </aside>
          </Transition>
        </Teleport>

        <main class="flex-1 min-w-0">
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-sidebar-enter-active,
.slide-sidebar-leave-active {
  transition: transform 0.3s ease;
}
.slide-sidebar-enter-from,
.slide-sidebar-leave-to {
  transform: translateX(-100%);
}
</style>
