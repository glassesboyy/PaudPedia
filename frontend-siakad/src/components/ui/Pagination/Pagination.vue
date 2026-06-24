<script setup lang="ts">
import { computed } from 'vue'
import BaseButton from '@/components/ui/Button/Button.vue'
import Icon from '@/components/ui/Icon/Icon.vue'

const props = defineProps<{
  currentPage: number
  lastPage: number
  totalItems?: number
  itemsPerPage?: number
}>()

const emit = defineEmits<{
  (e: 'page-change', page: number): void
}>()

const pages = computed(() => {
  const current = props.currentPage
  const last = props.lastPage
  const delta = 2 // How many pages to show before and after current
  const left = current - delta
  const right = current + delta + 1
  const range = []
  const rangeWithDots = []
  let l

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= left && i < right)) {
      range.push(i)
    }
  }

  for (const i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  }

  return rangeWithDots
})

const startItem = computed(() => {
  if (!props.totalItems || !props.itemsPerPage || props.totalItems === 0) return 0
  return (props.currentPage - 1) * props.itemsPerPage + 1
})

const endItem = computed(() => {
  if (!props.totalItems || !props.itemsPerPage || props.totalItems === 0) return 0
  return Math.min(props.currentPage * props.itemsPerPage, props.totalItems)
})
</script>

<template>
  <div v-if="lastPage > 1" class="px-6 py-4 bg-white/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
    <!-- Info -->
    <div class="text-sm text-slate-500 font-medium">
      <template v-if="totalItems && itemsPerPage">
        Menampilkan <span class="font-bold text-slate-700">{{ startItem }}</span> hingga <span class="font-bold text-slate-700">{{ endItem }}</span> dari <span class="font-bold text-slate-700">{{ totalItems }}</span> data
      </template>
      <template v-else-if="totalItems">
        Total <span class="font-bold text-slate-700">{{ totalItems }}</span> data
      </template>
    </div>

    <!-- Navigation -->
    <div class="flex items-center gap-1.5">
      <!-- Prev -->
      <button
        class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 border"
        :class="currentPage === 1 ? 'bg-slate-50 text-slate-300 border-slate-100 cursor-not-allowed' : 'bg-white text-slate-600 border-slate-200 hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50 shadow-sm'"
        :disabled="currentPage === 1"
        @click="emit('page-change', currentPage - 1)"
      >
        <Icon name="lucide:chevron-left" class="w-4 h-4" />
      </button>

      <!-- Pages -->
      <div class="hidden sm:flex items-center gap-1.5">
        <template v-for="(page, idx) in pages" :key="idx">
          <span v-if="page === '...'" class="w-9 text-center text-slate-400 font-medium">...</span>
          <button
            v-else
            class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold transition-all duration-200 border"
            :class="page === currentPage ? 'bg-primary-600 text-white border-primary-600 shadow-md shadow-primary-600/20' : 'bg-white text-slate-600 border-slate-200 hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50 shadow-sm'"
            @click="emit('page-change', page as number)"
          >
            {{ page }}
          </button>
        </template>
      </div>
      
      <!-- Mobile Info -->
      <div class="sm:hidden px-3 text-sm font-bold text-slate-700">
        {{ currentPage }} / {{ lastPage }}
      </div>

      <!-- Next -->
      <button
        class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 border"
        :class="currentPage === lastPage ? 'bg-slate-50 text-slate-300 border-slate-100 cursor-not-allowed' : 'bg-white text-slate-600 border-slate-200 hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50 shadow-sm'"
        :disabled="currentPage === lastPage"
        @click="emit('page-change', currentPage + 1)"
      >
        <Icon name="lucide:chevron-right" class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>
