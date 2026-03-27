<script setup lang="ts">
/**
 * GuestLayout — Minimal layout for public/guest pages (landing, etc.).
 * Simple navbar with logo + login button, and a minimal footer.
 */
import { RouterView } from 'vue-router'
import BaseButton from '@/components/ui/Button/Button.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background">
    <!-- Navbar -->
    <header class="sticky top-0 z-sticky bg-surface/80 backdrop-blur-md border-b border-border-muted">
      <div class="container flex items-center justify-between h-16">
        <!-- Logo -->
        <RouterLink to="/welcome" class="flex items-center gap-2.5 no-underline">
          <div>
            <span class="text-lg font-bold text-heading">PaudPedia</span>
            <span class="text-xs font-semibold text-primary-600 ml-1.5 bg-primary-50 px-1.5 py-0.5 rounded">SIAKAD</span>
          </div>
        </RouterLink>

        <!-- Actions -->
        <div class="flex items-center gap-3">
          <RouterLink to="/login">
            <BaseButton variant="primary" size="md">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              Masuk
            </BaseButton>
          </RouterLink>
        </div>
      </div>
    </header>

    <!-- Main content -->
    <main class="flex-1">
      <slot>
        <RouterView />
      </slot>
    </main>

    <!-- Footer -->
    <footer class="border-t border-border-muted bg-surface">
      <div class="container py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <p class="text-sm text-muted">
            &copy; {{ new Date().getFullYear() }} PaudPedia SIAKAD. All rights reserved.
          </p>
          <div class="flex items-center gap-6 text-sm text-muted">
            <a href="https://paudpedia.com/about" target="_blank" class="hover:text-primary-600 transition-colors">
              Tentang Kami
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
