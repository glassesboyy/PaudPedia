<script setup lang="ts">
/**
 * About Us Page
 * Route: /about
 *
 * FR-PP-10: Visi, Misi, Mentor — Mentor data from API.
 */
import type { FeaturedMentor } from '~~/types'

useSeo({
  title: 'Tentang Kami',
  description: 'Pelajari lebih lanjut tentang PaudPedia, visi, misi, dan mentor di balik platform pendidikan anak usia dini.',
})

const milestones = [
  { year: '2024', label: 'Awal Mula', event: 'PaudPedia didirikan dengan visi memajukan pendidikan anak usia dini di Indonesia.' },
  { year: '2025', label: 'Peluncuran', event: 'Peluncuran platform e-learning dengan 50+ kursus dan 20+ mentor berpengalaman.' },
  { year: '2026', label: 'Ekspansi', event: 'Ekspansi ke marketplace produk edukasi dan webinar interaktif untuk pendidik PAUD.' },
]

// ─── Featured Mentors from API ───
const mentors = ref<FeaturedMentor[]>([])
const mentorsLoading = ref(true)
const mentorsError = ref(false)

async function fetchMentors() {
  mentorsLoading.value = true
  mentorsError.value = false
  try {
    const { mentorService } = await import('~~/services')
    const res = await mentorService.getFeatured()
    if (res.success && res.data) {
      mentors.value = res.data
    }
    else {
      mentorsError.value = true
    }
  }
  catch {
    mentorsError.value = true
  }
  finally {
    mentorsLoading.value = false
  }
}

/** Split comma-separated expertise string into array */
function expertiseTags(expertise: string): string[] {
  return expertise ? expertise.split(',').map(s => s.trim()).filter(Boolean) : []
}

onMounted(fetchMentors)
</script>

<template>
  <div>
    <!-- Hero -->
    <PageHero
      badge="Tentang Kami"
      badge-icon="lucide:heart"
      title="Membangun Masa Depan"
      highlight="Pendidikan Anak"
      description="PaudPedia adalah platform e-learning dan marketplace yang didedikasikan untuk meningkatkan kualitas pendidikan anak usia dini di Indonesia."
    />

    <!-- Visi & Misi -->
    <section class="bg-surface">
      <div class="container py-14 sm:py-20">
        <div class="text-center mb-10 sm:mb-14">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
            <Icon name="lucide:compass" class="w-3.5 h-3.5 text-primary-500" />
            <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Visi & Misi</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold text-heading">Arah & Tujuan Kami</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
          <!-- Visi -->
          <div class="group p-6 sm:p-8 rounded-2xl border border-border bg-surface hover:border-primary-200 hover:shadow-medium hover:-translate-y-1 transition-all duration-300">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary-50 group-hover:bg-primary-100 group-hover:scale-110 transition-all duration-300 mb-5">
              <Icon name="lucide:eye" class="w-6 h-6 text-primary-600" />
            </div>
            <h3 class="text-lg font-semibold text-heading mb-3">Visi</h3>
            <p class="text-sm text-body leading-relaxed">
              Menjadi platform pendidikan anak usia dini terdepan di Indonesia yang membantu
              menciptakan generasi emas melalui pendidikan berkualitas, aksesibel, dan inovatif.
            </p>
          </div>
          <!-- Misi -->
          <div class="group p-6 sm:p-8 rounded-2xl border border-border bg-surface hover:border-secondary-200 hover:shadow-medium hover:-translate-y-1 transition-all duration-300">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-secondary-50 group-hover:bg-secondary-100 group-hover:scale-110 transition-all duration-300 mb-5">
              <Icon name="lucide:target" class="w-6 h-6 text-secondary-600" />
            </div>
            <h3 class="text-lg font-semibold text-heading mb-3">Misi</h3>
            <ul class="text-sm text-body leading-relaxed space-y-3">
              <li class="flex items-start gap-2.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-success-50 shrink-0 mt-0.5">
                  <Icon name="lucide:check" class="w-3 h-3 text-success-600" />
                </span>
                <span>Menyediakan materi pembelajaran PAUD yang berkualitas dan terstruktur.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-success-50 shrink-0 mt-0.5">
                  <Icon name="lucide:check" class="w-3 h-3 text-success-600" />
                </span>
                <span>Memfasilitasi pendidik dan orang tua dengan akses ke sumber belajar yang terjangkau.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-success-50 shrink-0 mt-0.5">
                  <Icon name="lucide:check" class="w-3 h-3 text-success-600" />
                </span>
                <span>Membangun komunitas pendidik PAUD yang saling mendukung dan berkolaborasi.</span>
              </li>
              <li class="flex items-start gap-2.5">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-success-50 shrink-0 mt-0.5">
                  <Icon name="lucide:check" class="w-3 h-3 text-success-600" />
                </span>
                <span>Menghadirkan inovasi dalam metode pembelajaran anak usia dini melalui teknologi.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Timeline -->
    <section class="bg-surface-muted">
      <div class="container py-14 sm:py-20">
        <div class="text-center mb-10 sm:mb-14">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
            <Icon name="lucide:clock" class="w-3.5 h-3.5 text-primary-500" />
            <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Timeline</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold text-heading">Perjalanan Kami</h2>
        </div>
        <div class="max-w-2xl mx-auto relative">
          <div class="absolute left-8 top-0 bottom-0 w-px bg-border hidden sm:block" />
          <div class="space-y-8">
            <div
              v-for="milestone in milestones"
              :key="milestone.year"
              class="flex gap-5 items-start group"
            >
              <div class="relative shrink-0 w-16 h-16 rounded-2xl border-2 border-primary-200 bg-surface flex items-center justify-center z-10 group-hover:border-primary-400 group-hover:shadow-md transition-all duration-300">
                <span class="text-sm font-bold text-primary-700">{{ milestone.year }}</span>
              </div>
              <div class="pt-1 pb-2">
                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-primary-50 text-primary-600 mb-2">
                  {{ milestone.label }}
                </span>
                <p class="text-sm text-body leading-relaxed">{{ milestone.event }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Mentor Kami -->
    <section class="bg-surface">
      <div class="container py-14 sm:py-20">
        <div class="text-center mb-10 sm:mb-14">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
            <Icon name="lucide:users" class="w-3.5 h-3.5 text-primary-500" />
            <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Mentor Kami</span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-bold text-heading">Belajar dari yang Terbaik</h2>
          <p class="mt-3 text-base text-body max-w-2xl mx-auto">
            Mentor berpengalaman yang siap membimbing Anda dalam pendidikan anak usia dini.
          </p>
        </div>

        <!-- Loading skeleton -->
        <SkeletonCardGrid v-if="mentorsLoading" :count="3" :columns="3" variant="profile" class="max-w-4xl mx-auto" />

        <!-- Error state -->
        <div v-else-if="mentorsError" class="text-center py-8">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-danger-50 mb-3">
            <Icon name="lucide:alert-circle" class="w-6 h-6 text-danger-500" />
          </div>
          <p class="text-sm text-body mb-3">Gagal memuat data mentor.</p>
          <button
            type="button"
            class="text-sm text-primary-600 font-medium hover:underline"
            @click="fetchMentors"
          >
            Coba lagi
          </button>
        </div>

        <!-- Mentor Grid -->
        <div v-else-if="mentors.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
          <div
            v-for="mentor in mentors"
            :key="mentor.id"
            class="group text-center p-6 rounded-2xl border border-border bg-surface hover:border-primary-200 hover:shadow-medium hover:-translate-y-1 transition-all duration-300"
          >
            <!-- Avatar -->
            <div class="relative w-20 h-20 mx-auto mb-4">
              <img
                v-if="mentor.photo_url"
                :src="mentor.photo_url"
                :alt="mentor.name"
                class="w-full h-full rounded-2xl object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-else class="w-full h-full rounded-2xl bg-primary-50 flex items-center justify-center group-hover:bg-primary-100 transition-colors duration-300">
                <Icon name="lucide:user" class="w-8 h-8 text-primary-400" />
              </div>
            </div>

            <!-- Name & Title -->
            <h3 class="text-sm font-semibold text-heading group-hover:text-primary-600 transition-colors">
              {{ mentor.name }}
            </h3>
            <p class="text-xs text-muted mt-0.5">{{ mentor.title }}</p>

            <!-- Expertise Tags -->
            <div v-if="mentor.expertise" class="mt-2 flex flex-wrap justify-center gap-1">
              <span
                v-for="skill in expertiseTags(mentor.expertise).slice(0, 2)"
                :key="skill"
                class="inline-block px-2 py-0.5 text-[10px] font-medium rounded-full bg-primary-50 text-primary-600"
              >
                {{ skill }}
              </span>
            </div>

            <!-- Bio -->
            <p class="text-xs text-body mt-3 leading-relaxed line-clamp-2">{{ mentor.bio }}</p>

            <!-- Stats -->
            <div class="mt-3 flex items-center justify-center gap-4 text-xs text-muted">
              <span class="flex items-center gap-1">
                <Icon name="lucide:book-open" class="w-3 h-3" />
                {{ mentor.courses_count }} Kursus
              </span>
              <span class="flex items-center gap-1">
                <Icon name="lucide:video" class="w-3 h-3" />
                {{ mentor.webinars_count }} Webinar
              </span>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-else class="text-center py-8">
          <p class="text-sm text-muted">Belum ada data mentor.</p>
        </div>

        <!-- View all link -->
        <div v-if="!mentorsLoading && !mentorsError && mentors.length > 0" class="mt-8 text-center">
          <NuxtLink
            to="/mentors"
            class="inline-flex items-center gap-2 text-sm text-primary-600 font-medium hover:underline"
          >
            Lihat Semua Mentor
            <Icon name="lucide:arrow-right" class="w-4 h-4" />
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <CtaSection />
  </div>
</template>
