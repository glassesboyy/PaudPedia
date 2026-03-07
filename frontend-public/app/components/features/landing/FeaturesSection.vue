<script setup lang="ts">
/**
 * FeaturesSection — Highlights platform key features.
 *
 * Fully static content. Uses design system tokens exclusively.
 */

const features = [
  {
    icon: 'lucide:book-open',
    title: 'Kursus Berkualitas',
    description: 'Materi pembelajaran yang dirancang oleh tenaga ahli pendidikan anak usia dini.',
    color: 'text-primary-600 bg-primary-50',
  },
  {
    icon: 'lucide:video',
    title: 'Webinar Interaktif',
    description: 'Sesi live bersama mentor berpengalaman untuk diskusi dan tanya jawab langsung.',
    color: 'text-secondary-600 bg-secondary-50',
  },
  {
    icon: 'lucide:package',
    title: 'Produk Edukasi',
    description: 'Buku, modul, dan media pembelajaran digital siap pakai untuk kegiatan belajar.',
    color: 'text-success-600 bg-success-50',
  },
  {
    icon: 'lucide:newspaper',
    title: 'Artikel Gratis',
    description: 'Baca artikel informatif seputar tumbuh kembang dan pendidikan anak usia dini.',
    color: 'text-warning-600 bg-warning-50',
  },
  {
    icon: 'lucide:award',
    title: 'Sertifikat Resmi',
    description: 'Dapatkan sertifikat setelah menyelesaikan kursus sebagai bukti kompetensi Anda.',
    color: 'text-danger-600 bg-danger-50',
  },
  {
    icon: 'lucide:layout-dashboard',
    title: 'Terintegrasi SIAKAD',
    description: 'Terhubung langsung dengan Sistem Informasi Akademik untuk pengelolaan data sekolah secara terpadu.',
    color: 'text-primary-600 bg-primary-50',
  },
]
</script>

<template>
  <section class="relative w-full bg-surface overflow-hidden">
    <!-- Decorative background shapes -->
    <div class="absolute top-0 right-0 w-72 h-72 rounded-full bg-primary-50/50 -translate-y-1/2 translate-x-1/3" />
    <div class="absolute bottom-0 left-0 w-56 h-56 rounded-full bg-secondary-50/40 translate-y-1/3 -translate-x-1/4" />
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full border border-border-muted/30" />

    <div class="container py-14 sm:py-20 relative z-10">
      <!-- Section header with badge -->
      <div class="text-center mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 mb-4">
          <Icon name="lucide:zap" class="w-3.5 h-3.5 text-primary-500" />
          <span class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Fitur Platform</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-heading">
          Kenapa Memilih PaudPedia?
        </h2>
        <p class="mt-3 text-base text-body max-w-2xl mx-auto">
          Kami menyediakan berbagai layanan untuk mendukung pendidikan anak usia dini yang berkualitas.
        </p>
      </div>

      <!-- Feature cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <div
          v-for="feature in features"
          :key="feature.title"
          class="group relative p-6 sm:p-7 rounded-2xl border border-border bg-surface hover:shadow-medium hover:border-primary-200 hover:-translate-y-1 transition-all duration-300"
        >
          <div :class="['inline-flex items-center justify-center w-12 h-12 rounded-xl mb-5 transition-transform duration-300 group-hover:scale-110', feature.color]">
            <Icon :name="feature.icon" class="w-6 h-6" />
          </div>
          <h3 class="text-base font-semibold text-heading mb-2">
            {{ feature.title }}
          </h3>
          <p class="text-sm text-body leading-relaxed">
            {{ feature.description }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
