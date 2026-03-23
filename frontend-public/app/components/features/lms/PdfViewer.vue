<script setup lang="ts">
import { ref, watch } from 'vue'
import VuePdfEmbed from 'vue-pdf-embed'

const props = defineProps<{
  pdfUrl: string
}>()

const isLoading = ref(true)
const pageCount = ref(1)
const currentPage = ref(1)
const scale = ref(1.0)
const isFullscreen = ref(false)

const pdfRef = ref<InstanceType<typeof VuePdfEmbed> | null>(null)

function handleDocumentRender() {
  isLoading.value = false
  // In vue-pdf-embed v2, this might be available this way
  if (pdfRef.value) {
    pageCount.value = (pdfRef.value as any).pageCount || 1
  }
}

function handleLoadError(error: Error) {
  isLoading.value = false
  console.error('Failed to load PDF:', error)
}

function zoomIn() {
  scale.value = Math.min(scale.value + 0.25, 3.0)
}

function zoomOut() {
  scale.value = Math.max(scale.value - 0.25, 0.5)
}

</script>

<template>
  <div class="flex flex-col h-full bg-surface-sunken" :class="{ 'fixed inset-0 z-50 bg-background': isFullscreen }">
    <!-- Sticky Toolbar -->
    <div class="sticky top-0 z-10 flex items-center justify-between p-3 border-b border-border bg-surface shadow-sm">
      <div class="flex items-center gap-2">
        <UButton variant="ghost" size="sm" @click="zoomOut" :disabled="scale <= 0.5">
          <Icon name="lucide:zoom-out" class="w-4 h-4" />
        </UButton>
        <span class="text-xs font-medium w-12 text-center">{{ Math.round(scale * 100) }}%</span>
        <UButton variant="ghost" size="sm" @click="zoomIn" :disabled="scale >= 3.0">
          <Icon name="lucide:zoom-in" class="w-4 h-4" />
        </UButton>
      </div>
      
      <div class="flex items-center gap-2">
        <span class="text-xs text-muted">Halaman</span>
        <input 
          v-model.number="currentPage" 
          type="number" 
          min="1" 
          :max="pageCount"
          class="w-16 text-center text-xs form-input rounded-md border-border-muted" 
        />
        <span class="text-xs text-muted">/ {{ pageCount }}</span>
      </div>
      
      <div>
        <UButton variant="ghost" size="sm" @click="isFullscreen = !isFullscreen">
          <Icon :name="isFullscreen ? 'lucide:minimize' : 'lucide:maximize'" class="w-4 h-4" />
        </UButton>
      </div>
    </div>

    <!-- Viewer Area -->
    <div class="flex-1 overflow-auto relative p-4 custom-scrollbar">
      <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-surface-sunken z-0">
        <div class="text-center">
          <Icon name="lucide:loader-circle" class="w-8 h-8 text-primary-500 animate-spin mx-auto mb-3" />
          <p class="text-sm text-muted">Memuat dokumen PDF...</p>
        </div>
      </div>
      
      <div class="flex justify-center transition-transform duration-200" :style="{ transform: `scale(${scale})`, transformOrigin: 'top center' }">
        <VuePdfEmbed
          ref="pdfRef"
          :source="pdfUrl"
          :page="currentPage"
          @rendered="handleDocumentRender"
          @error="handleLoadError"
          class="bg-white shadow-md border border-border"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 20px;
}
</style>
