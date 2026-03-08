<script setup lang="ts">
/**
 * ArticleCard — Reusable article preview card.
 *
 * Displays featured image, category badge, title, excerpt, author, date, reading time.
 * Uses design system tokens exclusively.
 */
import type { Article } from '~~/types';

defineProps<{
  article: Article
}>()

function formatDate(dateStr: string): string {
  try {
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    })
  } catch {
    return dateStr
  }
}

function getImageUrl(article: Article): string | null {
  return article.featured_image_url || (article as any).thumbnail_url || null
}

function normalizeTags(tags: unknown): string[] {
  if (Array.isArray(tags)) return tags
  if (typeof tags === 'string' && tags.trim()) return tags.split(',').map(t => t.trim())
  return []
}
</script>

<template>
  <NuxtLink
    :to="`/articles/${article.slug}`"
    class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-card hover:-translate-y-0.5 transition-all duration-300"
  >
    <!-- Featured image -->
    <div class="relative aspect-[16/10] overflow-hidden bg-primary-50/30">
      <img
        v-if="getImageUrl(article)"
        :src="getImageUrl(article)!"
        :alt="article.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <Icon name="lucide:newspaper" class="w-10 h-10 text-muted/30" />
      </div>

      <!-- Category badge overlay -->
      <span
        v-if="article.category"
        class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/90 text-primary-700 backdrop-blur-sm shadow-sm"
      >
        {{ article.category.name }}
      </span>

      <!-- Featured badge -->
      <span
        v-if="article.is_featured"
        class="absolute top-3 right-3 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[11px] font-semibold bg-warning-500/90 text-white backdrop-blur-sm"
      >
        <Icon name="lucide:star" class="w-3 h-3" />
        Featured
      </span>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-5">
      <!-- Tags -->
      <div v-if="normalizeTags(article.tags).length" class="flex flex-wrap gap-1.5 mb-2.5">
        <span
          v-for="tag in normalizeTags(article.tags).slice(0, 3)"
          :key="tag"
          class="text-[10px] font-medium text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full"
        >
          #{{ tag }}
        </span>
      </div>

      <h3 class="text-base font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors leading-snug">
        {{ article.title }}
      </h3>

      <p class="text-sm text-body/80 line-clamp-2 mb-4 leading-relaxed">
        {{ article.excerpt }}
      </p>

      <!-- Footer: Author / Date / Reading time -->
      <div class="mt-auto pt-3 border-t border-border/50 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
          <!-- Author avatar placeholder -->
          <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
            <span v-if="article.author" class="text-[10px] font-bold text-primary-600">
              {{ article.author.name.charAt(0) }}
            </span>
          </div>
          <div class="min-w-0">
            <p v-if="article.author" class="text-xs font-medium text-heading truncate">{{ article.author.name }}</p>
            <p class="text-[11px] text-muted">
              {{ article.published_date || formatDate(article.published_at) }}
            </p>
          </div>
        </div>

        <span class="text-[11px] text-muted shrink-0 flex items-center gap-1">
          <Icon name="lucide:clock" class="w-3 h-3" />
          {{ article.reading_time || 1 }} min
        </span>
      </div>
    </div>
  </NuxtLink>
</template>
