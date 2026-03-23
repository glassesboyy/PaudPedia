<script setup lang="ts">
/**
 * Webinar Detail Page
 * Route: /webinars/:slug
 *
 * FR-WB-P02: Display full webinar detail — schedule, price, description, mentor info
 */
import DOMPurify from 'isomorphic-dompurify'
import type { WebinarDetail } from '~~/types'

const route = useRoute()
const slug = route.params.slug as string

// ─── Cart ───
const { addToCart, hasItem, isAddingItem } = useCart()
const isInCart = computed(() => webinar.value ? hasItem(webinar.value.id, 'webinar') : false)
const isAdding = computed(() => webinar.value ? isAddingItem(webinar.value.id, 'webinar') : false)

function handleAddToCart() {
  if (!webinar.value || isInCart.value) return
  addToCart({
    id: webinar.value.id,
    type: 'webinar',
    name: webinar.value.title,
    slug: webinar.value.slug,
    price: webinar.value.price,
    thumbnail: webinar.value.thumbnail_url || '',
  })
}

// ─── State ───
const webinar = ref<WebinarDetail | null>(null)
const loading = ref(true)
const error = ref(false)

// ─── Helpers ───
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

function formatPrice(price: number): string {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price)
}

function formatDuration(min: number | null): string {
  if (!min) return '-'
  if (min < 60) return `${min} menit`
  const h = Math.floor(min / 60)
  const m = min % 60
  return m > 0 ? `${h} jam ${m} menit` : `${h} jam`
}

// ─── Fetch ───
async function fetchWebinar() {
  loading.value = true
  error.value = false
  try {
    const { webinarService } = await import('~~/services')
    const res = await webinarService.getBySlug(slug)
    if (res.success && res.data) {
      webinar.value = res.data
    } else {
      error.value = true
    }
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchWebinar)

// Dynamic SEO
watchEffect(() => {
  if (webinar.value) {
    useSeo({
      title: webinar.value.title,
      description: webinar.value.description?.replace(/<[^>]*>/g, '').slice(0, 160) || `Webinar ${webinar.value.title} di PaudPedia`,
      image: webinar.value.thumbnail_url || undefined,
    })
  }
})

// ─── Computed ───
const sanitizedDescription = computed(() => {
  if (!webinar.value?.description) return ''
  return DOMPurify.sanitize(webinar.value.description)
})

const hasSocialMedia = computed(() => {
  if (!webinar.value?.mentor?.social_media) return false
  return Object.values(webinar.value.mentor.social_media).some(v => v && String(v).trim())
})

const activeSocials = computed(() => {
  if (!webinar.value?.mentor?.social_media) return []
  return Object.entries(webinar.value.mentor.social_media)
    .filter(([, v]) => v && String(v).trim())
    .map(([platform, value]) => ({ platform, value: String(value) }))
})

const parseExpertise = computed(() => {
  if (!webinar.value?.mentor?.expertise) return []
  return webinar.value.mentor.expertise.split(',').map((s: string) => s.trim()).filter(Boolean)
})

const statusConfig = computed(() => {
  if (!webinar.value) return { label: '', color: '', icon: '' }
  if (webinar.value.is_upcoming) {
    return { label: 'Akan Datang', color: 'bg-success-50 text-success-700 border-success-200', icon: 'lucide:calendar-clock' }
  }
  return { label: 'Selesai', color: 'bg-gray-50 text-gray-600 border-gray-200', icon: 'lucide:calendar-check' }
})
</script>

<template>
  <div>
    <!-- Loading -->
    <SkeletonDetailContent v-if="loading" variant="webinar" />

    <!-- Error / Not Found -->
    <div v-else-if="error || !webinar" class="min-h-[60vh] flex items-center justify-center">
      <div class="text-center py-20">
        <div class="w-16 h-16 rounded-2xl bg-warning-50 flex items-center justify-center mx-auto mb-5">
          <Icon name="lucide:alert-triangle" class="w-8 h-8 text-warning-500" />
        </div>
        <h3 class="text-lg font-semibold text-heading mb-2">Webinar Tidak Ditemukan</h3>
        <p class="text-sm text-body mb-6 max-w-sm mx-auto">
          Webinar yang Anda cari tidak ditemukan atau mungkin sudah tidak tersedia.
        </p>
        <div class="flex items-center justify-center gap-3">
          <NuxtLink to="/webinars">
            <button
              type="button"
              class="inline-flex items-center gap-2 px-5 py-2 rounded-xl border border-border text-sm font-medium text-body hover:border-secondary-300 transition-colors"
            >
              <Icon name="lucide:arrow-left" class="w-4 h-4" />
              Kembali
            </button>
          </NuxtLink>
          <button
            type="button"
            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-secondary-500 text-white text-sm font-medium hover:bg-secondary-600 transition-colors shadow-sm"
            @click="fetchWebinar"
          >
            <Icon name="lucide:refresh-cw" class="w-4 h-4" />
            Coba Lagi
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <template v-else>
      <!-- ─── Hero ─── -->
      <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-secondary-100/60 via-surface to-primary-100/40" />
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(var(--color-secondary-200),.35),transparent)]" />
        <!-- Decorative shapes -->
        <div class="absolute top-[10%] left-[5%] w-20 h-20 rounded-2xl bg-secondary-300/15 rotate-12 blur-sm" />
        <div class="absolute top-[15%] right-[8%] w-14 h-14 rounded-full border-[3px] border-primary-300/20" />
        <div class="absolute bottom-[12%] right-[10%] w-24 h-24 rounded-3xl bg-secondary-200/15 -rotate-6 blur-sm" />

        <div class="container py-10 sm:py-14 relative z-10">
          <!-- Breadcrumb -->
          <nav class="flex items-center gap-2 text-xs text-muted mb-6">
            <NuxtLink to="/" class="hover:text-secondary-600 transition-colors">Beranda</NuxtLink>
            <Icon name="lucide:chevron-right" class="w-3 h-3" />
            <NuxtLink to="/webinars" class="hover:text-secondary-600 transition-colors">Webinar</NuxtLink>
            <Icon name="lucide:chevron-right" class="w-3 h-3" />
            <span class="text-heading font-medium truncate max-w-[200px]">{{ webinar.title }}</span>
          </nav>

          <div class="flex flex-col md:flex-row gap-6 md:gap-8 items-start">
            <!-- Thumbnail -->
            <div class="w-full md:w-72 lg:w-80 shrink-0">
              <div class="relative rounded-2xl overflow-hidden shadow-medium border-4 border-white aspect-video">
                <img
                  v-if="webinar.thumbnail_url"
                  :src="webinar.thumbnail_url"
                  :alt="webinar.title"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full bg-secondary-50 flex items-center justify-center">
                  <Icon name="lucide:video" class="w-12 h-12 text-secondary-300" />
                </div>
                <!-- Status badge -->
                <span
                  class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold border backdrop-blur-sm"
                  :class="statusConfig.color"
                >
                  <Icon :name="statusConfig.icon" class="w-3 h-3" />
                  {{ statusConfig.label }}
                </span>
                <!-- Discount -->
                <span
                  v-if="webinar.has_discount && webinar.discount_percentage"
                  class="absolute top-3 right-3 px-2 py-0.5 rounded-full bg-danger-500 text-white text-[10px] font-bold shadow-sm"
                >
                  -{{ webinar.discount_percentage }}%
                </span>
              </div>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary-50 border border-secondary-100 mb-3">
                <Icon name="lucide:video" class="w-3.5 h-3.5 text-secondary-500" />
                <span class="text-xs font-semibold text-secondary-600 uppercase tracking-wider">Webinar</span>
              </div>

              <h1 class="text-2xl sm:text-3xl font-bold text-heading leading-tight">
                {{ webinar.title }}
              </h1>

              <!-- Schedule pills -->
              <div class="mt-4 flex flex-wrap gap-3">
                <span v-if="webinar.scheduled_date" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/80 border border-border text-xs font-medium text-body backdrop-blur-sm">
                  <Icon name="lucide:calendar" class="w-3.5 h-3.5 text-secondary-500" />
                  {{ webinar.scheduled_day ? `${webinar.scheduled_day}, ` : '' }}{{ webinar.scheduled_date }}
                </span>
                <span v-if="webinar.scheduled_time" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/80 border border-border text-xs font-medium text-body backdrop-blur-sm">
                  <Icon name="lucide:clock" class="w-3.5 h-3.5 text-secondary-500" />
                  {{ webinar.scheduled_time }} WIB
                </span>
                <span v-if="webinar.duration_minutes" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/80 border border-border text-xs font-medium text-body backdrop-blur-sm">
                  <Icon name="lucide:timer" class="w-3.5 h-3.5 text-secondary-500" />
                  {{ formatDuration(webinar.duration_minutes) }}
                </span>
              </div>

              <!-- Price -->
              <div class="mt-4">
                <template v-if="!webinar.price || webinar.price === 0">
                  <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-success-50 border border-success-200 text-success-700 text-sm font-bold">
                    <Icon name="lucide:gift" class="w-4 h-4" />
                    GRATIS
                  </span>
                </template>
                <template v-else>
                  <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-secondary-600">{{ formatPrice(webinar.price) }}</span>
                    <span
                      v-if="webinar.has_discount && webinar.original_price"
                      class="text-sm text-muted line-through"
                    >
                      {{ formatPrice(webinar.original_price) }}
                    </span>
                  </div>
                </template>
              </div>

              <!-- Participants -->
              <p v-if="webinar.max_participants" class="mt-3 text-xs text-muted flex items-center gap-1.5">
                <Icon name="lucide:users" class="w-3.5 h-3.5" />
                Maksimal {{ webinar.max_participants }} peserta
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- ─── Body ─── -->
      <section class="bg-surface">
        <div class="container py-10 sm:py-14">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Main column -->
            <div class="lg:col-span-2 space-y-10">
              <!-- Description -->
              <div v-if="sanitizedDescription">
                <h2 class="text-lg font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:file-text" class="w-5 h-5 text-secondary-500" />
                  Deskripsi Webinar
                </h2>
                <div
                  class="webinar-content prose prose-sm max-w-none text-body leading-relaxed"
                  v-html="sanitizedDescription"
                />
              </div>
              <div v-else class="py-8 text-center">
                <p class="text-sm text-muted">Belum ada deskripsi untuk webinar ini.</p>
              </div>

              <!-- Mentor Section (part of main content on mobile, if no sidebar) -->
              <div v-if="webinar.mentor" class="lg:hidden">
                <h2 class="text-lg font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:user-circle" class="w-5 h-5 text-secondary-500" />
                  Tentang Mentor
                </h2>
                <NuxtLink
                  :to="`/mentors/${webinar.mentor.id}`"
                  class="block p-5 rounded-2xl border border-border bg-surface hover:border-secondary-200 hover:shadow-card transition-all"
                >
                  <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0">
                      <img
                        v-if="webinar.mentor.photo_url"
                        :src="webinar.mentor.photo_url"
                        :alt="webinar.mentor.name"
                        class="w-full h-full object-cover"
                      />
                      <div v-else class="w-full h-full bg-secondary-50 flex items-center justify-center">
                        <Icon name="lucide:user" class="w-6 h-6 text-secondary-300" />
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-semibold text-heading">{{ webinar.mentor.name }}</h4>
                      <p v-if="webinar.mentor.title" class="text-xs text-muted mt-0.5">{{ webinar.mentor.title }}</p>
                    </div>
                    <Icon name="lucide:chevron-right" class="w-4 h-4 text-muted shrink-0" />
                  </div>
                  <p v-if="webinar.mentor.bio" class="text-xs text-body mt-3 line-clamp-3 leading-relaxed">
                    {{ webinar.mentor.bio }}
                  </p>
                </NuxtLink>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
              <!-- Schedule card -->
              <div class="p-5 rounded-2xl border border-border bg-surface">
                <h3 class="text-sm font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:calendar-days" class="w-4 h-4 text-secondary-500" />
                  Detail Jadwal
                </h3>
                <dl class="space-y-3 text-sm">
                  <div v-if="webinar.scheduled_date" class="flex items-start gap-3">
                    <dt class="text-muted shrink-0 w-20">Tanggal</dt>
                    <dd class="text-heading font-medium">{{ webinar.scheduled_day ? `${webinar.scheduled_day}, ` : '' }}{{ webinar.scheduled_date }}</dd>
                  </div>
                  <div v-if="webinar.scheduled_time" class="flex items-start gap-3">
                    <dt class="text-muted shrink-0 w-20">Waktu</dt>
                    <dd class="text-heading font-medium">{{ webinar.scheduled_time }} WIB</dd>
                  </div>
                  <div v-if="webinar.duration_minutes" class="flex items-start gap-3">
                    <dt class="text-muted shrink-0 w-20">Durasi</dt>
                    <dd class="text-heading font-medium">{{ formatDuration(webinar.duration_minutes) }}</dd>
                  </div>
                  <div v-if="webinar.max_participants" class="flex items-start gap-3">
                    <dt class="text-muted shrink-0 w-20">Kuota</dt>
                    <dd class="text-heading font-medium">{{ webinar.max_participants }} peserta</dd>
                  </div>
                </dl>
              </div>

              <!-- Price card -->
              <div class="p-5 rounded-2xl border border-border bg-surface">
                <h3 class="text-sm font-semibold text-heading mb-3 flex items-center gap-2">
                  <Icon name="lucide:tag" class="w-4 h-4 text-secondary-500" />
                  Harga
                </h3>
                <div>
                  <template v-if="!webinar.price || webinar.price === 0">
                    <span class="text-lg font-bold text-success-600">Gratis</span>
                  </template>
                  <template v-else>
                    <p class="text-2xl font-bold text-secondary-600">{{ formatPrice(webinar.price) }}</p>
                    <p v-if="webinar.has_discount && webinar.original_price" class="text-xs text-muted line-through mt-0.5">
                      {{ formatPrice(webinar.original_price) }}
                    </p>
                    <span
                      v-if="webinar.has_discount && webinar.discount_percentage"
                      class="inline-block mt-2 px-2 py-0.5 rounded bg-danger-50 text-danger-600 text-[10px] font-semibold"
                    >
                      Hemat {{ webinar.discount_percentage }}%
                    </span>
                  </template>
                </div>

                <!-- Action Buttons -->
                <div v-if="webinar.is_owned" class="mt-4">
                  <NuxtLink
                    to="/account/webinars"
                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-success-50 text-success-700 border border-success-200 hover:bg-success-100 transition-all shadow-sm"
                  >
                    <Icon name="lucide:calendar-check" class="w-4 h-4" />
                    Ikuti Webinar
                  </NuxtLink>
                  <p class="text-[11px] text-center text-muted mt-2">
                     Anda sudah terdaftar di webinar ini.
                  </p>
                </div>
                <div v-else-if="webinar.price && webinar.price > 0" class="mt-4">
                  <button
                    type="button"
                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm"
                    :class="isInCart
                      ? 'bg-success-50 text-success-700 border border-success-200 cursor-default'
                      : 'bg-secondary-500 text-white hover:bg-secondary-600'"
                    :disabled="isInCart || isAdding"
                    @click="handleAddToCart"
                  >
                    <Icon v-if="isAdding" name="lucide:loader-2" class="w-4 h-4 animate-spin" />
                    <Icon v-else :name="isInCart ? 'lucide:check-circle' : 'lucide:shopping-cart'" class="w-4 h-4" />
                    {{ isAdding ? 'Menambahkan...' : isInCart ? 'Sudah di Keranjang' : 'Tambah ke Keranjang' }}
                  </button>
                  <NuxtLink
                    v-if="isInCart"
                    to="/cart"
                    class="mt-2 w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-border text-xs font-medium text-body hover:border-secondary-300 hover:text-secondary-600 hover:bg-secondary-50/50 transition-all"
                  >
                    <Icon name="lucide:arrow-right" class="w-3.5 h-3.5" />
                    Lihat Keranjang
                  </NuxtLink>
                </div>
              </div>

              <!-- Mentor card (sidebar – desktop) -->
              <div v-if="webinar.mentor" class="hidden lg:block p-5 rounded-2xl border border-border bg-surface">
                <h3 class="text-sm font-semibold text-heading mb-4 flex items-center gap-2">
                  <Icon name="lucide:user-circle" class="w-4 h-4 text-secondary-500" />
                  Mentor
                </h3>

                <NuxtLink :to="`/mentors/${webinar.mentor.id}`" class="group flex items-center gap-3 mb-4">
                  <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 ring-2 ring-transparent group-hover:ring-secondary-200 transition-all">
                    <img
                      v-if="webinar.mentor.photo_url"
                      :src="webinar.mentor.photo_url"
                      :alt="webinar.mentor.name"
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full bg-secondary-50 flex items-center justify-center">
                      <Icon name="lucide:user" class="w-5 h-5 text-secondary-300" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-heading group-hover:text-secondary-600 transition-colors truncate">
                      {{ webinar.mentor.name }}
                    </h4>
                    <p v-if="webinar.mentor.title" class="text-xs text-muted truncate">{{ webinar.mentor.title }}</p>
                  </div>
                </NuxtLink>

                <p v-if="webinar.mentor.bio" class="text-xs text-body leading-relaxed line-clamp-4 mb-3">
                  {{ webinar.mentor.bio }}
                </p>

                <!-- Expertise -->
                <div v-if="parseExpertise.length" class="flex flex-wrap gap-1.5 mb-3">
                  <span
                    v-for="skill in parseExpertise"
                    :key="skill"
                    class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-secondary-50 text-secondary-700 border border-secondary-100"
                  >
                    {{ skill }}
                  </span>
                </div>

                <!-- Social links -->
                <div v-if="hasSocialMedia" class="flex flex-wrap gap-2 pt-3 border-t border-border">
                  <a
                    v-for="social in activeSocials"
                    :key="social.platform"
                    :href="getSocialUrl(social.platform, social.value)"
                    :title="socialLabels[social.platform] || social.platform"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-8 h-8 rounded-lg border border-border flex items-center justify-center text-muted hover:text-secondary-600 hover:border-secondary-300 transition-colors"
                  >
                    <Icon :name="socialIcons[social.platform] || 'lucide:link'" class="w-3.5 h-3.5" />
                  </a>
                </div>

                <NuxtLink
                  :to="`/mentors/${webinar.mentor.id}`"
                  class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-secondary-600 hover:text-secondary-700 hover:underline"
                >
                  Lihat Profil
                  <Icon name="lucide:arrow-right" class="w-3 h-3" />
                </NuxtLink>
              </div>

              <!-- Back link -->
              <NuxtLink
                to="/webinars"
                class="inline-flex items-center gap-2 text-sm font-medium text-muted hover:text-secondary-600 transition-colors"
              >
                <Icon name="lucide:arrow-left" class="w-4 h-4" />
                Semua Webinar
              </NuxtLink>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped lang="postcss">
/* Webinar description HTML content styles */
.webinar-content :deep(h1),
.webinar-content :deep(h2),
.webinar-content :deep(h3),
.webinar-content :deep(h4) {
  @apply text-heading font-semibold mt-6 mb-3;
}
.webinar-content :deep(h2) { @apply text-lg; }
.webinar-content :deep(h3) { @apply text-base; }
.webinar-content :deep(p) { @apply mb-4 leading-relaxed; }
.webinar-content :deep(ul),
.webinar-content :deep(ol) { @apply pl-5 mb-4 space-y-1.5; }
.webinar-content :deep(ul) { @apply list-disc; }
.webinar-content :deep(ol) { @apply list-decimal; }
.webinar-content :deep(li) { @apply leading-relaxed; }
.webinar-content :deep(a) { @apply text-secondary-600 hover:underline; }
.webinar-content :deep(img) { @apply rounded-xl my-4 max-w-full h-auto; }
.webinar-content :deep(blockquote) {
  @apply border-l-4 border-secondary-200 pl-4 my-4 italic text-muted;
}
.webinar-content :deep(table) { @apply w-full border-collapse my-4; }
.webinar-content :deep(th),
.webinar-content :deep(td) {
  @apply border border-border px-3 py-2 text-sm text-left;
}
.webinar-content :deep(th) { @apply bg-surface-muted font-semibold; }
</style>
