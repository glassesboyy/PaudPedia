<script setup lang="ts">
/**
 * LandingView — SIAKAD landing/welcome page.
 *
 * Sections:
 *   1. Hero — tagline, description, CTA buttons
 *   2. Features — 4 benefit cards
 *   3. CTA — encourage school registration
 */
import BaseButton from '@/components/ui/Button/Button.vue'

const features = [
  {
    icon: 'school',
    title: 'Manajemen Data Siswa & Guru',
    description: 'Kelola data siswa, guru, dan kelas dalam satu platform terpusat yang mudah diakses.',
  },
  {
    icon: 'calendar',
    title: 'Absensi Digital Otomatis',
    description: 'Input absensi harian dengan cepat dan pantau kehadiran siswa secara real-time.',
  },
  {
    icon: 'chart',
    title: 'Laporan Perkembangan Anak',
    description: 'Monitor dan laporkan perkembangan setiap anak kepada orang tua dengan mudah.',
  },
  {
    icon: 'shield',
    title: 'Keamanan Data Terjamin',
    description: 'Data sekolah Anda tersimpan aman dengan enkripsi dan backup otomatis.',
  },
]

const featureIcons: Record<string, string> = {
  school: 'lucide:users',
  calendar: 'lucide:calendar-check',
  chart: 'lucide:line-chart',
  shield: 'lucide:shield-check',
}
</script>

<template>
  <div>
    <!-- ═══════════════════════════════════════════════════
         HERO SECTION
         ═══════════════════════════════════════════════════ -->
    <section class="relative overflow-hidden">
      <!-- Background gradient -->
      <div class="absolute inset-0 bg-gradient-to-br from-primary-50 via-background to-secondary-50/30" />
      <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-primary-100/30 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl" />
      <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-secondary-100/20 rounded-full translate-y-1/2 -translate-x-1/3 blur-3xl" />

      <div class="container relative z-10 py-20 sm:py-28 lg:py-36">
        <div class="max-w-3xl mx-auto text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-6 animate-fade-in">
            <div class="w-2 h-2 rounded-full bg-primary-500 animate-pulse" />
            <span class="text-xs font-semibold text-primary-700 uppercase tracking-wider">Sistem Informasi Akademik</span>
          </div>

          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-heading leading-tight animate-fade-in-up">
            Kelola Sekolah PAUD
            <br />
            <span class="gradient-text">Lebih Mudah & Efisien</span>
          </h1>

          <p class="mt-6 text-lg sm:text-xl text-body max-w-2xl mx-auto leading-relaxed animate-fade-in-up">
            SIAKAD PaudPedia adalah platform manajemen sekolah PAUD yang modern. 
            Kelola data siswa, absensi, dan perkembangan anak dalam satu sistem terintegrasi.
          </p>

          <!-- CTA Buttons -->
          <div class="mt-10 flex flex-wrap items-center justify-center gap-4 animate-fade-in-up">
            <RouterLink to="/login">
              <BaseButton variant="primary" size="lg">
                <Icon name="lucide:log-in" class="w-5 h-5" />
                Masuk ke SIAKAD
              </BaseButton>
            </RouterLink>
            <a href="https://paudpedia.com/auth/register" target="_blank">
              <BaseButton variant="outline" size="lg">
                Daftarkan Sekolah
                <Icon name="lucide:building" class="w-4 h-4" />
              </BaseButton>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         FEATURES SECTION
         ═══════════════════════════════════════════════════ -->
    <section class="section bg-surface">
      <div class="container">
        <div class="text-center max-w-2xl mx-auto mb-14">
          <h2 class="text-heading">
            Semua yang Anda Butuhkan untuk
            <span class="text-primary-600">Kelola Sekolah</span>
          </h2>
          <p class="mt-4 text-body">
            Fitur lengkap untuk mempermudah administrasi dan manajemen sekolah PAUD Anda.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="group relative p-6 rounded-2xl bg-background border border-border-muted hover:border-primary-200 hover:shadow-medium transition-all duration-300"
          >
            <!-- Icon -->
            <div class="w-12 h-12 rounded-xl bg-primary-50 group-hover:bg-primary-100 flex items-center justify-center mb-4 transition-colors duration-300">
              <Icon :name="featureIcons[feature.icon]" class="w-6 h-6 text-primary-600" />
            </div>

            <h3 class="text-base font-semibold text-heading mb-2">{{ feature.title }}</h3>
            <p class="text-sm text-body leading-relaxed">{{ feature.description }}</p>

            <!-- Hover accent -->
            <div class="absolute inset-x-0 bottom-0 h-0.5 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300" />
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════
         CTA SECTION
         ═══════════════════════════════════════════════════ -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800">
      <div class="container py-16 sm:py-20 relative z-10">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-2xl sm:text-3xl font-bold text-white leading-tight">
            Siap Mendigitalkan Sekolah Anda?
          </h2>
          <p class="mt-4 text-primary-100 text-lg leading-relaxed">
            Daftarkan akun Anda terlebih dahulu di PaudPedia, lalu mulai mendaftarkan sekolah untuk menggunakan SIAKAD.
          </p>

          <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="https://paudpedia.com/auth/register" target="_blank">
              <BaseButton variant="secondary" size="lg" class="shadow-strong">
                Daftar Akun Gratis
                <Icon name="lucide:arrow-right" class="w-4 h-4" />
              </BaseButton>
            </a>
            <RouterLink to="/login">
              <BaseButton variant="ghost" size="lg" class="!text-white hover:!bg-white/10">
                Sudah Punya Akun? Masuk
              </BaseButton>
            </RouterLink>
          </div>

          <!-- Info text -->
          <p class="mt-6 text-sm text-primary-200">
            Gratis untuk sekolah dengan maksimal 20 siswa. Upgrade kapan saja.
          </p>
        </div>
      </div>

      <!-- Decorative circles -->
      <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-white/[0.04]" />
      <div class="absolute -bottom-20 -right-20 w-72 h-72 rounded-full bg-white/[0.04]" />
    </section>
  </div>
</template>
