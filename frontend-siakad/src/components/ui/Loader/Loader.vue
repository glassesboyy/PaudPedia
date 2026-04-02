<script setup lang="ts">
/**
 * BaseLoader — Reusable full-section loading component.
 * Provides a standardized visual experience for data fetching.
 */

interface LoaderProps {
  text?: string
  minHeight?: string
}

withDefaults(defineProps<LoaderProps>(), {
  text: 'Memuat data...',
  minHeight: '200px'
})
</script>

<template>
  <div 
    class="flex flex-col items-center justify-center gap-4 animate-fade-in w-full"
    :style="{ minHeight: minHeight }"
  >
    <div class="relative">
      <!-- Pulsing background square -->
      <div class="w-12 h-12 rounded-xl bg-primary-50 animate-pulse"></div>
      
      <!-- Center spinner -->
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="w-6 h-6 rounded-full border-[3px] border-primary-600 border-t-transparent animate-spin"></div>
      </div>
    </div>
    
    <!-- Animated text -->
    <p class="text-[11px] font-black text-slate-400 animate-pulse tracking-widest uppercase">
      {{ text }}
    </p>
  </div>
</template>
