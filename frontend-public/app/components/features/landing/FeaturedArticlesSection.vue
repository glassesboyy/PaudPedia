<script setup lang="ts">
/**
 * FeaturedArticlesSection — Showcases latest articles on landing page.
 *
 * Receives article data via props from the parent landing page.
 * Uses design system tokens exclusively.
 */
import type { Article } from '~~/types';

defineProps<{
  articles: Article[]
}>()
</script>

<template>
  <section class="relative w-full bg-surface overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute top-0 right-0 w-72 h-72 rounded-full bg-primary-50/30 translate-x-1/4 -translate-y-1/4" />
    <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-secondary-50/30 -translate-x-1/3 translate-y-1/3" />

    <div class="container py-14 sm:py-20 relative z-10">
      <!-- Section header with badge -->
      <div class="text-center mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
          <Icon name="lucide:newspaper" class="w-3.5 h-3.5 text-primary-500" />
          <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Artikel Terbaru</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-heading">
          Wawasan & Inspirasi
        </h2>
        <p class="mt-3 text-base text-body max-w-2xl mx-auto">
          Temukan artikel pilihan seputar pendidikan anak usia dini, tips mengajar, dan perkembangan anak.
        </p>
      </div>

      <!-- Article cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <ArticleCard
          v-for="article in articles.slice(0, 6)"
          :key="article.id"
          :article="article"
        />
      </div>

      <!-- Footer -->
      <div v-if="articles.length" class="mt-10 text-center">
        <NuxtLink to="/articles">
          <UButton variant="outline" size="md" class="group/btn">
            Lihat Semua Artikel
            <Icon name="lucide:arrow-right" class="w-4 h-4 ml-1.5 group-hover/btn:translate-x-1 transition-transform duration-200" />
          </UButton>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
