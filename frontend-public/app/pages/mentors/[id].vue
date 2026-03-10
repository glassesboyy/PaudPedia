<script setup lang="ts">
/**
 * Mentor Profile / Detail Page
 * Route: /mentors/:id
 *
 * FR-MN-P02: Display mentor profile (bio, photo, expertise, social media)
 * FR-MN-P03: Display webinars & courses taught by the mentor
 */
import type { MentorDetail } from '~~/types'

const route = useRoute()
const mentorId = Number(route.params.id)

const mentor = ref<MentorDetail | null>(null)
const loading = ref(true)
const error = ref(false)

const socialIcons: Record<string, string> = {
  instagram: 'lucide:instagram',
  facebook: 'lucide:facebook',
  youtube: 'lucide:youtube',
  linkedin: 'lucide:linkedin',
  twitter: 'lucide:twitter',
  tiktok: 'simple-icons:tiktok',
  telegram: 'lucide:send',
  discord: 'simple-icons:discord',
}

const socialLabels: Record<string, string> = {
  instagram: 'Instagram',
  facebook: 'Facebook',
  youtube: 'YouTube',
  linkedin: 'LinkedIn',
  twitter: 'Twitter / X',
  tiktok: 'TikTok',
  telegram: 'Telegram',
  discord: 'Discord',
}

function parseExpertise(expertise: string | undefined): string[] {
  if (!expertise) return []
  return expertise.split(',').map(s => s.trim()).filter(Boolean)
}

function formatPrice(price: number): string {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price)
}

function getLevelLabel(level: string): string {
  const labels: Record<string, string> = {
    beginner: 'Pemula',
    intermediate: 'Menengah',
    advanced: 'Lanjutan',
  }
  return labels[level] || level
}

function getLevelColor(level: string): string {
  const colors: Record<string, string> = {
    beginner: 'bg-success-50 text-success-700 border-success-200',
    intermediate: 'bg-warning-50 text-warning-700 border-warning-200',
    advanced: 'bg-danger-50 text-danger-700 border-danger-200',
  }
  return colors[level] || 'bg-primary-50 text-primary-700 border-primary-200'
}

function getSocialUrl(platform: string, value: string): string {
  if (value.startsWith('http')) return value
  const prefixes: Record<string, string> = {
    instagram: 'https://instagram.com/',
    facebook: 'https://facebook.com/',
    youtube: 'https://youtube.com/',
    linkedin: 'https://linkedin.com/in/',
    twitter: 'https://twitter.com/',
    tiktok: 'https://tiktok.com/@',
    telegram: 'https://t.me/',
    discord: 'https://discord.gg/',
  }
  const prefix = prefixes[platform]
  if (prefix) {
    const handle = value.replace(/^@/, '')
    return `${prefix}${handle}`
  }
  return value
}

async function fetchMentor() {
  loading.value = true
  error.value = false
  try {
    const { mentorService } = await import('~~/services')
    const res = await mentorService.getById(mentorId)
    if (res.success) {
      mentor.value = res.data
    } else {
      error.value = true
    }
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchMentor)

// Dynamic SEO
watchEffect(() => {
  if (mentor.value) {
    useSeo({
      title: mentor.value.name,
      description: mentor.value.bio || `Profil mentor ${mentor.value.name} di PaudPedia`,
    })
  }
})

const hasSocialMedia = computed(() => {
  if (!mentor.value?.social_media) return false
  return Object.values(mentor.value.social_media).some(v => v && String(v).trim())
})

const activeSocials = computed(() => {
  if (!mentor.value?.social_media) return []
  return Object.entries(mentor.value.social_media)
    .filter(([, v]) => v && String(v).trim())
    .map(([platform, value]) => ({ platform, value: String(value) }))
})
</script>

<template>
  <div>
    <!-- Loading -->
    <SkeletonDetailContent v-if="loading" variant="profile" />

    <!-- Error -->
    <div v-else-if="error || !mentor" class="min-h-[60vh] flex items-center justify-center">
      <div class="text-center py-20">
        <div class="w-16 h-16 rounded-2xl bg-warning-50 flex items-center justify-center mx-auto mb-5">
          <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500" />
        </div>
        <h3 class="text-lg font-semibold text-heading mb-2">Mentor Tidak Ditemukan</h3>
        <p class="text-sm text-body mb-6 max-w-sm mx-auto">Mentor yang Anda cari tidak ditemukan atau mungkin sudah tidak aktif.</p>
        <div class="flex items-center justify-center gap-3">
          <NuxtLink to="/mentors">
            <UButton variant="outline" size="sm">
              <Icon name="lucide:arrow-left" class="w-4 h-4 mr-1.5" />
              Kembali
            </UButton>
          </NuxtLink>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition-colors shadow-sm"
            @click="fetchMentor"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <template v-else>
      <!-- ─── Hero / Profile Header ─── -->
      <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-100/60 via-surface to-secondary-100/50" />
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(var(--color-primary-200),.35),transparent)]" />
        <!-- Floating shapes -->
        <div class="absolute top-[10%] left-[6%] w-20 h-20 rounded-2xl bg-primary-300/15 rotate-12 blur-sm" />
        <div class="absolute top-[15%] right-[8%] w-14 h-14 rounded-full border-[3px] border-secondary-300/20" />
        <div class="absolute bottom-[12%] right-[10%] w-24 h-24 rounded-3xl bg-primary-200/15 -rotate-6 blur-sm" />

        <div class="container py-10 sm:py-14 relative z-10">
          <!-- Breadcrumb -->
          <nav class="flex items-center gap-2 text-xs text-muted mb-6">
            <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Beranda</NuxtLink>
            <Icon name="lucide:chevron-right" class="w-3 h-3" />
            <NuxtLink to="/mentors" class="hover:text-primary-600 transition-colors">Mentor</NuxtLink>
            <Icon name="lucide:chevron-right" class="w-3 h-3" />
            <span class="text-heading font-medium truncate max-w-[200px]">{{ mentor.name }}</span>
          </nav>

          <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start">
            <!-- Photo -->
            <div class="relative w-32 h-32 sm:w-40 sm:h-40 shrink-0">
              <img
                v-if="mentor.photo_url"
                :src="mentor.photo_url"
                :alt="mentor.name"
                class="w-full h-full rounded-3xl object-cover shadow-medium border-4 border-white"
              />
              <div v-else class="w-full h-full rounded-3xl bg-primary-50 border-4 border-white shadow-medium flex items-center justify-center">
                <Icon name="lucide:user" class="w-14 h-14 text-primary-300" />
              </div>
            </div>

            <!-- Info -->
            <div class="flex-1 text-center md:text-left">
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-3">
                <Icon name="lucide:award" class="w-3.5 h-3.5 text-primary-500" />
                <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Mentor</span>
              </div>

              <h1 class="text-2xl sm:text-3xl font-bold text-heading">
                {{ mentor.name }}
              </h1>

              <p v-if="mentor.title" class="mt-1 text-base text-body">
                {{ mentor.title }}
              </p>

              <!-- Expertise tags -->
              <div v-if="mentor.expertise" class="mt-3 flex flex-wrap gap-2 justify-center md:justify-start">
                <span
                  v-for="skill in parseExpertise(mentor.expertise)"
                  :key="skill"
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-white/80 text-primary-700 border border-primary-100 backdrop-blur-sm"
                >
                  <Icon name="lucide:sparkles" class="w-3 h-3" />
                  {{ skill }}
                </span>
              </div>

              <!-- Stats row -->
              <div class="mt-4 flex items-center justify-center md:justify-start gap-6 text-sm text-body">
                <span class="flex items-center gap-1.5">
                  <Icon name="lucide:book-open" class="w-4 h-4 text-primary-500" />
                  <strong class="text-heading">{{ mentor.courses_count }}</strong> Kursus
                </span>
                <span class="flex items-center gap-1.5">
                  <Icon name="lucide:video" class="w-4 h-4 text-secondary-500" />
                  <strong class="text-heading">{{ mentor.webinars_count }}</strong> Webinar
                </span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ─── Body Content ─── -->
      <section class="bg-surface">
        <div class="container py-10 sm:py-14">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Main column -->
            <div class="lg:col-span-2 space-y-10">
              <!-- Bio -->
              <div v-if="mentor.bio">
                <h2 class="text-lg font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:user-circle" class="w-5 h-5 text-primary-500" />
                  Tentang Mentor
                </h2>
                <div class="prose prose-sm max-w-none text-body leading-relaxed">
                  <p class="whitespace-pre-line">{{ mentor.bio }}</p>
                </div>
              </div>

              <!-- Courses (FR-MN-P03) -->
              <div v-if="mentor.courses && mentor.courses.length > 0">
                <h2 class="text-lg font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:book-open" class="w-5 h-5 text-primary-500" />
                  Kursus yang Diampu
                  <span class="text-xs font-normal text-muted">({{ mentor.courses.length }})</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <NuxtLink
                    v-for="course in mentor.courses"
                    :key="course.id"
                    :to="`/courses/${course.slug}`"
                    class="group flex gap-4 p-4 rounded-xl border border-border bg-surface hover:border-primary-200 hover:shadow-card transition-all duration-200"
                  >
                    <!-- Thumbnail -->
                    <div class="w-16 h-16 rounded-lg overflow-hidden shrink-0 bg-primary-50/30">
                      <img
                        v-if="course.thumbnail_url"
                        :src="course.thumbnail_url"
                        :alt="course.title"
                        class="w-full h-full object-cover"
                        loading="lazy"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <Icon name="lucide:graduation-cap" class="w-6 h-6 text-primary-300" />
                      </div>
                    </div>
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-semibold text-heading line-clamp-2 group-hover:text-primary-600 transition-colors leading-snug">
                        {{ course.title }}
                      </h4>
                      <div class="mt-1.5 flex flex-wrap items-center gap-2">
                        <span
                          v-if="course.level"
                          class="text-[10px] font-medium px-2 py-0.5 rounded-full border"
                          :class="getLevelColor(course.level)"
                        >
                          {{ getLevelLabel(course.level) }}
                        </span>
                        <span v-if="course.category" class="text-[10px] text-muted">
                          {{ course.category.name }}
                        </span>
                      </div>
                      <p class="mt-1 text-xs font-semibold text-primary-600">
                        {{ formatPrice(course.price) }}
                      </p>
                    </div>
                  </NuxtLink>
                </div>
              </div>

              <!-- Webinars (FR-MN-P03) -->
              <div v-if="mentor.webinars && mentor.webinars.length > 0">
                <h2 class="text-lg font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:video" class="w-5 h-5 text-secondary-500" />
                  Webinar yang Diampu
                  <span class="text-xs font-normal text-muted">({{ mentor.webinars.length }})</span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <NuxtLink
                    v-for="webinar in mentor.webinars"
                    :key="webinar.id"
                    :to="`/webinars/${webinar.slug}`"
                    class="group flex gap-4 p-4 rounded-xl border border-border bg-surface hover:border-secondary-200 hover:shadow-card transition-all duration-200"
                  >
                    <!-- Thumbnail -->
                    <div class="w-16 h-16 rounded-lg overflow-hidden shrink-0 bg-secondary-50/30">
                      <img
                        v-if="webinar.thumbnail_url"
                        :src="webinar.thumbnail_url"
                        :alt="webinar.title"
                        class="w-full h-full object-cover"
                        loading="lazy"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <Icon name="lucide:video" class="w-6 h-6 text-secondary-300" />
                      </div>
                    </div>
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-semibold text-heading line-clamp-2 group-hover:text-secondary-600 transition-colors leading-snug">
                        {{ webinar.title }}
                      </h4>
                      <p v-if="webinar.scheduled_date" class="mt-1 text-xs text-muted flex items-center gap-1">
                        <Icon name="lucide:calendar" class="w-3 h-3" />
                        {{ webinar.scheduled_date }}
                      </p>
                      <p class="mt-1 text-xs font-semibold text-secondary-600">
                        {{ formatPrice(webinar.price) }}
                      </p>
                    </div>
                  </NuxtLink>
                </div>
              </div>

              <!-- No content note -->
              <div v-if="(!mentor.courses || mentor.courses.length === 0) && (!mentor.webinars || mentor.webinars.length === 0)" class="text-center py-10">
                <div class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-4">
                  <Icon name="lucide:calendar-clock" class="w-7 h-7 text-primary-400" />
                </div>
                <p class="text-sm text-body max-w-sm mx-auto">
                  Belum ada kursus atau webinar yang diampu oleh mentor ini. Nantikan konten terbaru!
                </p>
              </div>
            </div>

            <!-- Sidebar -->
            <aside class="space-y-6">
              <!-- Social Media -->
              <div v-if="hasSocialMedia" class="p-5 rounded-2xl border border-border bg-surface">
                <h3 class="text-sm font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:share-2" class="w-4 h-4 text-primary-500" />
                  Media Sosial
                </h3>
                <div class="space-y-2.5">
                  <a
                    v-for="{ platform, value } in activeSocials"
                    :key="platform"
                    :href="getSocialUrl(platform, value)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl border border-border bg-surface-muted hover:border-primary-200 hover:bg-primary-50/50 transition-all group"
                  >
                    <div class="w-8 h-8 rounded-lg bg-surface flex items-center justify-center border border-border group-hover:border-primary-200 transition-colors">
                      <Icon :name="socialIcons[platform] || 'lucide:link'" class="w-4 h-4 text-muted group-hover:text-primary-600 transition-colors" />
                    </div>
                    <div class="min-w-0">
                      <p class="text-xs font-medium text-heading">{{ socialLabels[platform] || platform }}</p>
                      <p class="text-[11px] text-muted truncate">{{ value }}</p>
                    </div>
                    <Icon name="lucide:external-link" class="w-3.5 h-3.5 text-muted/50 ml-auto shrink-0" />
                  </a>
                </div>
              </div>

              <!-- Quick stats card -->
              <div class="p-5 rounded-2xl border border-border bg-surface">
                <h3 class="text-sm font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:bar-chart-3" class="w-4 h-4 text-primary-500" />
                  Statistik
                </h3>
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-xs text-muted flex items-center gap-1.5">
                      <Icon name="lucide:book-open" class="w-3.5 h-3.5" />
                      Kursus
                    </span>
                    <span class="text-sm font-semibold text-heading">{{ mentor.courses_count }}</span>
                  </div>
                  <div class="h-px bg-border/50" />
                  <div class="flex items-center justify-between">
                    <span class="text-xs text-muted flex items-center gap-1.5">
                      <Icon name="lucide:video" class="w-3.5 h-3.5" />
                      Webinar
                    </span>
                    <span class="text-sm font-semibold text-heading">{{ mentor.webinars_count }}</span>
                  </div>
                </div>
              </div>

              <!-- Back to list -->
              <NuxtLink to="/mentors" class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-border text-sm font-medium text-body hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50/50 transition-all">
                <Icon name="lucide:arrow-left" class="w-4 h-4" />
                Lihat Semua Mentor
              </NuxtLink>
            </aside>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>
