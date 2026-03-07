<script setup lang="ts">
/**
 * SidebarLayout — Reusable sidebar + content layout.
 *
 * Shows one section at a time. Sidebar navigation switches the active section.
 * Used on Privacy Policy, Terms, and FAQ pages.
 *
 * Supports two rendering modes:
 * 1. Built-in: renders `content`, `list`, `contactLink` from the section data.
 * 2. Slot: if a default scoped slot is provided, it renders custom content
 *    receiving `{ section, index }`.
 */

import type { SidebarSection } from '~~/types';

interface Props {
  sections: SidebarSection[]
  /** Label above the sidebar nav (default: "Daftar Isi") */
  sidebarLabel?: string
}

const props = withDefaults(defineProps<Props>(), {
  sidebarLabel: 'Daftar Isi',
})

const slots = defineSlots<{
  default?: (props: { section: SidebarSection; index: number }) => unknown
}>()

const activeIndex = ref(0)

const currentSection = computed(() => (props.sections[activeIndex.value] ?? props.sections[0]) as SidebarSection)
</script>

<template>
  <section class="bg-surface">
    <div class="container py-14 sm:py-20">
      <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-10">
        <!-- Sidebar Nav (desktop) -->
        <aside class="hidden lg:block lg:col-span-1">
          <div class="sticky top-24">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-3">
              {{ sidebarLabel }}
            </p>
            <nav class="space-y-1">
              <button
                v-for="(section, idx) in sections"
                :key="section.id"
                type="button"
                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm text-left transition-all duration-200"
                :class="activeIndex === idx ? 'bg-primary-50 text-primary-700 font-medium shadow-sm' : 'text-body hover:text-heading hover:bg-surface-muted'"
                @click="activeIndex = idx"
              >
                <div
                  class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                  :class="activeIndex === idx ? 'bg-primary-100' : 'bg-surface-muted'"
                >
                  <Icon :name="section.icon" class="w-4 h-4" :class="activeIndex === idx ? 'text-primary-600' : 'text-muted'" />
                </div>
                <div class="min-w-0">
                  <span class="block line-clamp-1">{{ section.title }}</span>
                  <span v-if="section.subtitle" class="text-xs text-muted">{{ section.subtitle }}</span>
                </div>
              </button>
            </nav>
          </div>
        </aside>

        <!-- Mobile Nav Tabs -->
        <div class="lg:hidden -mt-6">
          <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
            <button
              v-for="(section, idx) in sections"
              :key="section.id"
              type="button"
              class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-medium border transition-colors"
              :class="activeIndex === idx ? 'bg-primary-50 border-primary-200 text-primary-700' : 'bg-surface border-border text-body hover:bg-surface-muted'"
              @click="activeIndex = idx"
            >
              <Icon :name="section.icon" class="w-3.5 h-3.5" />
              {{ section.title }}
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="lg:col-span-3">
          <!-- Active Section Header -->
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center">
              <Icon :name="currentSection.icon" class="w-5 h-5 text-primary-600" />
            </div>
            <div>
              <h2 class="text-lg font-semibold text-heading">{{ currentSection.title }}</h2>
              <p v-if="currentSection.subtitle" class="text-xs text-muted">{{ currentSection.subtitle }}</p>
            </div>
          </div>

          <!-- Slot-based content (FAQ, etc.) -->
          <template v-if="slots.default">
            <slot :section="currentSection" :index="activeIndex" />
          </template>

          <!-- Built-in content (Privacy, Terms) -->
          <template v-else>
            <div class="p-6 sm:p-7 rounded-2xl border border-border bg-surface">
              <p class="text-sm text-body leading-relaxed">
                {{ currentSection.content }}
                <NuxtLink
                  v-if="currentSection.contactLink"
                  to="/contact"
                  class="text-primary-600 font-medium hover:underline"
                >Kontak</NuxtLink><template v-if="currentSection.contactLink">.</template>
              </p>
              <ul v-if="currentSection.list" class="mt-4 space-y-2.5">
                <li
                  v-for="(item, idx) in currentSection.list"
                  :key="idx"
                  class="flex items-start gap-2.5 text-sm text-body leading-relaxed"
                >
                  <Icon name="lucide:circle-dot" class="w-4 h-4 text-primary-400 shrink-0 mt-0.5" />
                  <!-- eslint-disable-next-line vue/no-v-html -->
                  <span v-html="item" />
                </li>
              </ul>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-6">
              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                :class="activeIndex > 0 ? 'text-body hover:text-heading hover:bg-surface-muted' : 'text-muted/40 cursor-not-allowed'"
                :disabled="activeIndex <= 0"
                @click="activeIndex--"
              >
                <Icon name="lucide:chevron-left" class="w-4 h-4" />
                Sebelumnya
              </button>
              <span class="text-xs text-muted">{{ activeIndex + 1 }} / {{ sections.length }}</span>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                :class="activeIndex < sections.length - 1 ? 'text-body hover:text-heading hover:bg-surface-muted' : 'text-muted/40 cursor-not-allowed'"
                :disabled="activeIndex >= sections.length - 1"
                @click="activeIndex++"
              >
                Selanjutnya
                <Icon name="lucide:chevron-right" class="w-4 h-4" />
              </button>
            </div>
          </template>
        </div>
      </div>
    </div>
  </section>
</template>
