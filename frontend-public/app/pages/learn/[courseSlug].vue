<script setup lang="ts">
/**
 * LMS Course Parent Page (Persistent)
 * Route: /learn/:courseSlug
 *
 * This page PERSISTS when lessonSlug changes in the child route.
 * Contains: sidebar (course nav), course data loading, mobile sidebar toggle.
 * Content is rendered by the child <NuxtPage /> (lesson player).
 */
import { useLmsStore } from '~~/stores/lms'

definePageMeta({
  layout: 'lms',
  middleware: ['auth'],
})

const route = useRoute()
const store = useLmsStore()

// Set course slug in store
store.courseSlug = route.params.courseSlug as string

// Load course data once (persists across lesson changes)
onMounted(async () => {
  await store.loadPlayerData()

  // If we're at the bare /learn/:courseSlug (no lesson), child index.vue handles redirect
  // If we have a lessonSlug, the child [lessonSlug].vue handles loading lesson detail
})

onBeforeUnmount(() => {
  store.cleanup()
  store.$reset()
})

useSeo({ title: 'Belajar' })
</script>

<template>
  <div class="min-h-[calc(100vh-3.5rem)]">
    <!-- ═══════════ Loading Skeleton ═══════════ -->
    <div v-if="store.isCourseLoading" class="grid grid-cols-1 lg:grid-cols-[21rem_1fr] h-full">
      <aside class="hidden lg:flex flex-col border-r border-border bg-surface max-h-[calc(100vh-3.5rem)]">
        <div class="p-5 border-b border-border animate-pulse space-y-3">
          <div class="h-3 w-20 bg-muted/10 rounded" />
          <div class="h-5 w-3/4 bg-muted/10 rounded" />
          <div class="space-y-1.5 pt-1">
            <div class="flex items-center justify-between">
              <div class="h-3 w-24 bg-muted/10 rounded" />
              <div class="h-3 w-8 bg-muted/10 rounded" />
            </div>
            <div class="h-2 w-full bg-muted/10 rounded-full" />
          </div>
        </div>
        <div class="p-4 space-y-3 animate-pulse">
          <div v-for="n in 3" :key="n" class="space-y-2">
            <div class="h-4 w-32 bg-muted/10 rounded px-2" />
            <div v-for="m in 3" :key="m" class="h-12 w-full bg-muted/10 rounded-xl" />
          </div>
        </div>
      </aside>
      <section class="p-4 md:p-6 lg:p-8 animate-pulse space-y-4">
        <div class="max-w-5xl mx-auto space-y-5">
          <div class="rounded-2xl border border-border/30 p-5 space-y-3">
            <div class="flex items-center gap-2">
              <div class="h-6 w-16 bg-muted/10 rounded-full" />
            </div>
            <div class="h-7 w-2/3 bg-muted/10 rounded" />
            <div class="h-4 w-1/2 bg-muted/10 rounded" />
          </div>
          <div class="aspect-video rounded-2xl bg-muted/10" />
        </div>
      </section>
    </div>

    <!-- ═══════════ Error ═══════════ -->
    <div v-else-if="store.courseError" class="p-6 lg:p-10">
      <div class="max-w-xl mx-auto rounded-2xl border border-danger-200 bg-danger-50 p-8 text-center">
        <Icon name="lucide:wifi-off" class="w-8 h-8 text-danger-500 mx-auto mb-3" />
        <h2 class="text-lg font-semibold text-heading mb-2">Gagal Memuat LMS</h2>
        <p class="text-sm text-muted mb-5">{{ store.courseError }}</p>
        <UButton variant="primary" @click="store.loadPlayerData()">
          <Icon name="lucide:refresh-cw" class="w-4 h-4 mr-2" />
          Muat Ulang
        </UButton>
      </div>
    </div>

    <!-- ═══════════ Player Layout ═══════════ -->
    <div v-else-if="store.playerData" class="grid grid-cols-1 lg:grid-cols-[21rem_1fr] h-full">
      <!-- Mobile sidebar toggle (FAB) -->
      <button
        type="button"
        class="lg:hidden fixed bottom-5 right-5 z-40 w-12 h-12 rounded-full bg-primary-600 text-white shadow-lg flex items-center justify-center hover:bg-primary-700 transition-colors"
        @click="store.isSidebarOpen = !store.isSidebarOpen"
      >
        <Icon :name="store.isSidebarOpen ? 'lucide:x' : 'lucide:list'" class="w-5 h-5" />
      </button>

      <!-- Mobile overlay -->
      <Transition name="fade">
        <div
          v-if="store.isSidebarOpen"
          class="lg:hidden fixed inset-0 bg-black/40 z-30 backdrop-blur-sm"
          @click="store.isSidebarOpen = false"
        />
      </Transition>

      <!-- ─── Sidebar (PERSISTENT — never re-renders on lesson change) ─── -->
      <aside
        class="fixed lg:static inset-y-0 left-0 z-30 w-[85vw] max-w-[21rem] bg-surface border-r border-border flex flex-col max-h-[calc(100vh-3.5rem)] transition-transform duration-300 lg:translate-x-0"
        :class="store.isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
      >
        <!-- Course header -->
        <div class="p-5 border-b border-border">
          <p class="text-[10px] font-bold uppercase tracking-widest text-primary-500 mb-1.5">Course Player</p>
          <h1 class="text-sm font-bold text-heading line-clamp-2 mb-3 leading-snug">{{ store.playerData.course.title }}</h1>

          <div class="space-y-1.5">
            <div class="flex items-center justify-between text-[11px]">
              <span class="text-muted">
                {{ store.playerData.progress.completed_lessons }}/{{ store.playerData.progress.total_lessons }} selesai
              </span>
              <span class="font-bold" :class="store.isCourseCompleted ? 'text-success-600' : 'text-primary-600'">
                {{ store.playerData.progress.progress_percentage }}%
              </span>
            </div>
            <div class="h-1.5 rounded-full bg-surface-sunken overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-700 ease-out"
                :class="store.isCourseCompleted ? 'bg-success-500' : 'bg-primary-500'"
                :style="{ width: store.progressWidth }"
              />
            </div>
          </div>
        </div>

        <!-- Modules list (accordion) -->
        <div class="flex-1 overflow-y-auto">
          <div v-for="(module, mIdx) in store.playerData.course.modules" :key="module.id" class="border-b border-border/40 last:border-b-0">
            <!-- Module header (collapsible) -->
            <button
              type="button"
              class="w-full flex items-center gap-3 px-5 py-3 text-left hover:bg-primary-50/40 transition-colors"
              @click="store.toggleModule(module.id)"
            >
              <div class="w-6 h-6 rounded-md bg-primary-100 flex items-center justify-center flex-shrink-0">
                <span class="text-[10px] font-bold text-primary-600">{{ mIdx + 1 }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-heading truncate">{{ module.title }}</p>
                <p v-if="module.description" class="text-[10px] text-muted truncate">{{ module.description }}</p>
              </div>
              <Icon
                name="lucide:chevron-down"
                class="w-3.5 h-3.5 text-muted transition-transform duration-200 flex-shrink-0"
                :class="{ 'rotate-180': store.expandedModules.has(module.id) }"
              />
            </button>

            <!-- Lessons (collapsible) -->
            <div v-if="store.expandedModules.has(module.id)" class="pb-2 px-3">
              <NuxtLink
                v-for="lesson in module.lessons"
                :key="lesson.id"
                :to="`/learn/${store.courseSlug}/${lesson.slug}`"
                class="w-full flex items-start gap-2.5 rounded-lg px-3 py-2 mb-0.5 text-left transition-all duration-150"
                :class="lesson.slug === ($route.params.lessonSlug as string)
                  ? 'bg-primary-50 ring-1 ring-primary-200'
                  : 'hover:bg-surface-sunken/60'"
                @click="store.isSidebarOpen = false"
              >
                <div class="mt-0.5 flex-shrink-0">
                  <div
                    v-if="lesson.is_completed"
                    class="w-5 h-5 rounded-full bg-success-100 flex items-center justify-center"
                  >
                    <Icon name="lucide:check" class="w-3 h-3 text-success-600" />
                  </div>
                  <div
                    v-else-if="lesson.slug === ($route.params.lessonSlug as string)"
                    class="w-5 h-5 rounded-full bg-primary-500 flex items-center justify-center"
                  >
                    <Icon name="lucide:play" class="w-2.5 h-2.5 text-white" />
                  </div>
                  <div v-else class="w-5 h-5 rounded-full border-2 border-border-muted" />
                </div>
                <div class="min-w-0 flex-1">
                  <p
                    class="text-[13px] leading-snug truncate"
                    :class="lesson.slug === ($route.params.lessonSlug as string)
                      ? 'font-semibold text-primary-700'
                      : lesson.is_completed ? 'text-muted' : 'text-heading font-medium'"
                  >
                    {{ lesson.title }}
                  </p>
                  <p class="text-[10px] text-muted mt-0.5 flex items-center gap-1">
                    <Icon :name="store.lessonTypeIcon[lesson.type || ''] || 'lucide:file'" class="w-3 h-3" />
                    {{ store.lessonTypeLabel[lesson.type || ''] || 'Materi' }}
                    <span v-if="lesson.duration_minutes"> · {{ lesson.duration_minutes }} min</span>
                  </p>
                </div>
              </NuxtLink>
            </div>
          </div>
        </div>
      </aside>

      <!-- ─── Content Area (dynamic child page) ─── -->
      <section class="bg-surface-sunken/40 overflow-y-auto max-h-[calc(100vh-3.5rem)]">
        <NuxtPage />
      </section>
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
</style>
