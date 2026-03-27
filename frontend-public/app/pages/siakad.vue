<script setup lang="ts">
/**
 * SIAKAD Marketing Page
 * Route: /siakad
 *
 * Dedicated marketing page for the SIAKAD (School Management System).
 * Showcases features, pricing tiers, and CTA.
 */
useSeo({
  title: 'SIAKAD — Sistem Informasi Akademik PAUD',
  description: 'Kelola sekolah PAUD Anda dengan sistem digital terpadu. Manajemen siswa, absensi, rapor, dan keuangan dalam satu platform.',
})

const features = [
  {
    icon: 'lucide:users',
    title: 'Manajemen Siswa & Guru',
    description: 'Kelola data siswa, guru, dan orang tua dengan mudah. Dukung multi-kelas dan multi-guru.',
  },
  {
    icon: 'lucide:calendar-check',
    title: 'Absensi Digital',
    description: 'Input absensi harian oleh guru. Orang tua bisa pantau kehadiran anak secara real-time.',
  },
  {
    icon: 'lucide:file-text',
    title: 'Rapor Digital',
    description: 'Generate rapor perkembangan anak dalam format PDF. Tersedia template sesuai kurikulum PAUD.',
  },
  {
    icon: 'lucide:shield-check',
    title: 'Multi-Tenant & Aman',
    description: 'Setiap sekolah memiliki data terpisah. Enkripsi end-to-end dan keamanan tingkat enterprise.',
  },
  {
    icon: 'lucide:smartphone',
    title: 'Monitoring Orang Tua',
    description: 'Orang tua dapat memantau perkembangan anak, kehadiran, dan nilai langsung dari dashboard.',
  },
  {
    icon: 'lucide:bar-chart-3',
    title: 'Laporan & Analitik',
    description: 'Dashboard analitik untuk kepala sekolah. Pantau statistik kehadiran, keuangan, dan performa.',
  },
]

const plans = [
  {
    name: 'Free',
    price: 'Gratis',
    description: 'Untuk sekolah yang baru memulai digitalisasi.',
    features: [
      'Maksimal 20 siswa',
      'Maksimal 5 guru',
      'Manajemen kelas',
      'Absensi digital',
      'Dashboard orang tua',
    ],
    cta: 'Mulai Gratis',
    highlighted: false,
  },
  {
    name: 'Pro',
    price: 'Rp 200.000',
    period: '/bulan',
    description: 'Untuk sekolah yang ingin fitur lengkap tanpa batasan.',
    features: [
      'Unlimited siswa',
      'Unlimited guru',
      'Semua fitur Free',
      'Generate PDF rapor',
      'Manajemen keuangan (SPP & Tabungan)',
      'Laporan analitik lanjutan',
      'Prioritas support',
    ],
    cta: 'Daftar Sekarang',
    highlighted: true,
  },
]

import { useAuth } from '~~/app/composables/useAuth'
import { useAuthStore } from '~~/stores/auth'

const { isAuthenticated } = useAuth()
const authStore = useAuthStore()
const hasSchoolAccess = computed(() => authStore.hasSchoolAccess)
const siakadUrl = computed(() => authStore.siakadUrl)
const tokenCookie = useCookie('auth_token')
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900">
      <div class="container py-20 sm:py-28 lg:py-32 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/10 mb-6">
            <Icon name="lucide:layout-dashboard" class="w-4 h-4 text-primary-200" />
            <span class="text-xs font-semibold text-primary-100 uppercase tracking-wider">Sistem SIAKAD</span>
          </div>

          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">
            Kelola Sekolah PAUD Anda dengan
            <span class="text-secondary-300">Sistem Digital Terpadu</span>
          </h1>

          <p class="mt-6 text-lg sm:text-xl text-primary-100 leading-relaxed max-w-2xl mx-auto">
            SIAKAD PaudPedia adalah platform SaaS untuk manajemen sekolah PAUD.
            Dari absensi hingga rapor, semua dalam satu dashboard yang mudah digunakan.
          </p>

          <div class="mt-10 flex flex-wrap gap-4 justify-center">
            <template v-if="hasSchoolAccess">
              <a :href="`${siakadUrl}/auth/token?token=${tokenCookie}`">
                <UButton variant="secondary" size="lg" class="px-8 shadow-strong">
                  Buka Dashboard SIAKAD
                  <Icon name="lucide:external-link" class="w-4 h-4 ml-2" />
                </UButton>
              </a>
            </template>
            <template v-else>
              <NuxtLink :to="isAuthenticated ? '/auth/register?type=school' : '/auth/register?type=school'">
                <UButton variant="secondary" size="lg" class="px-8 shadow-strong">
                  Daftarkan Sekolah Anda
                  <Icon name="lucide:arrow-right" class="w-4 h-4 ml-2" />
                </UButton>
              </NuxtLink>
            </template>
            <a href="#pricing">
              <UButton variant="ghost" size="lg" class="!text-white hover:!bg-white/10 px-8">
                Lihat Harga
              </UButton>
            </a>
          </div>
        </div>
      </div>

      <!-- Decorative -->
      <div class="absolute -top-20 -left-20 w-64 h-64 rounded-full bg-white/[0.04]" />
      <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-white/[0.04]" />
    </section>

    <!-- Features -->
    <section class="py-20 sm:py-28 bg-background">
      <div class="container">
        <div class="text-center max-w-2xl mx-auto mb-14">
          <h2 class="text-2xl sm:text-3xl font-bold text-heading">Fitur Lengkap untuk Sekolah PAUD</h2>
          <p class="mt-4 text-body">
            Semua yang Anda butuhkan untuk mengelola sekolah PAUD secara digital, efisien, dan modern.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="group p-6 rounded-2xl border border-border hover:border-primary-200 bg-surface hover:shadow-medium transition-all duration-300"
          >
            <div class="w-12 h-12 rounded-xl bg-primary-50 group-hover:bg-primary-100 flex items-center justify-center mb-4 transition-colors">
              <Icon :name="feature.icon" class="w-6 h-6 text-primary-600" />
            </div>
            <h3 class="text-lg font-semibold text-heading mb-2">{{ feature.title }}</h3>
            <p class="text-sm text-body leading-relaxed">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-20 sm:py-28 bg-surface-muted">
      <div class="container">
        <div class="text-center max-w-2xl mx-auto mb-14">
          <h2 class="text-2xl sm:text-3xl font-bold text-heading">Pilih Paket yang Sesuai</h2>
          <p class="mt-4 text-body">
            Mulai gratis, upgrade kapan saja sesuai kebutuhan sekolah Anda.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">
          <div
            v-for="plan in plans"
            :key="plan.name"
            :class="[
              'relative p-8 rounded-2xl border-2 transition-all duration-300',
              plan.highlighted
                ? 'border-primary-500 bg-surface shadow-strong scale-[1.02]'
                : 'border-border bg-surface hover:border-primary-200 hover:shadow-medium',
            ]"
          >
            <!-- Popular badge -->
            <div v-if="plan.highlighted" class="absolute -top-3 left-1/2 -translate-x-1/2">
              <span class="px-4 py-1 rounded-full bg-primary-600 text-white text-xs font-bold uppercase tracking-wider">
                Populer
              </span>
            </div>

            <h3 class="text-xl font-bold text-heading">{{ plan.name }}</h3>
            <div class="mt-3 flex items-baseline gap-1">
              <span class="text-3xl font-bold text-heading">{{ plan.price }}</span>
              <span v-if="plan.period" class="text-sm text-muted">{{ plan.period }}</span>
            </div>
            <p class="mt-3 text-sm text-body">{{ plan.description }}</p>

            <ul class="mt-6 space-y-3">
              <li v-for="feat in plan.features" :key="feat" class="flex items-start gap-2.5">
                <Icon name="lucide:check" class="w-4 h-4 text-success-500 mt-0.5 flex-shrink-0" />
                <span class="text-sm text-body">{{ feat }}</span>
              </li>
            </ul>

            <NuxtLink to="/auth/register?type=school" class="block mt-8">
              <UButton
                :variant="plan.highlighted ? 'primary' : 'outline'"
                size="lg"
                block
              >
                {{ plan.cta }}
              </UButton>
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Bottom CTA -->
    <section class="py-20 sm:py-24 bg-background">
      <div class="container text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-heading">Siap Memulai?</h2>
        <p class="mt-4 text-lg text-body max-w-xl mx-auto">
          Daftarkan sekolah Anda sekarang dan nikmati kemudahan manajemen sekolah PAUD secara digital.
        </p>
        <div class="mt-8">
          <NuxtLink to="/auth/register?type=school">
            <UButton variant="primary" size="lg" class="px-10">
              Daftarkan Sekolah Anda
              <Icon name="lucide:arrow-right" class="w-4 h-4 ml-2" />
            </UButton>
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>
