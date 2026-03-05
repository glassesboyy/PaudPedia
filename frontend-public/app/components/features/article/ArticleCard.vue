<script setup lang="ts">
/**
 * ArticleCard — Reusable article preview card.
 *
 * Displays featured image, category, title, excerpt, author, date, reading time.
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
</script>

<template>
  <NuxtLink
    :to="`/articles/${article.slug}`"
    class="group flex flex-col rounded-2xl border border-border bg-surface overflow-hidden hover:shadow-card transition-all duration-300"
  >
    <!-- Featured image -->
    <div class="relative aspect-video overflow-hidden bg-surface-sunken">
      <img
        v-if="article.thumbnail_url || (article as any).featured_image_url"
        :src="article.thumbnail_url || (article as any).featured_image_url"
        :alt="article.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <Icon name="lucide:newspaper" class="w-10 h-10 text-muted" />
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 flex flex-col p-4">
      <!-- Category -->
      <p v-if="article.category" class="text-xs text-primary-600 font-medium mb-1.5">
        {{ article.category.name }}
      </p>

      <h3 class="text-sm font-semibold text-heading line-clamp-2 mb-2 group-hover:text-primary-600 transition-colors">
        {{ article.title }}
      </h3>

      <p class="text-xs text-body line-clamp-2 mb-3">
        {{ article.excerpt }}
      </p>

      <div class="mt-auto pt-3 border-t border-border-muted flex items-center justify-between">
        <!-- Author & Date -->
        <div class="flex items-center gap-2 text-xs text-muted min-w-0">
          <span v-if="article.author" class="truncate">{{ article.author.name }}</span>
          <span v-if="article.published_at || (article as any).published_date" class="shrink-0">
            · {{ (article as any).published_date || formatDate(article.published_at) }}
          </span>
        </div>

        <!-- Reading time -->
        <span
          v-if="(article as any).reading_time"
          class="text-xs text-muted shrink-0 flex items-center gap-1"
        >
          <Icon name="lucide:clock" class="w-3.5 h-3.5" />
          {{ (article as any).reading_time }} min
        </span>
      </div>
    </div>
  </NuxtLink>
</template>
