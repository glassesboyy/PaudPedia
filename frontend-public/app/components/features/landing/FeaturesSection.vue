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
    icon: 'lucide:users',
    title: 'Komunitas PAUD',
    description: 'Bergabung dengan komunitas pendidik dan orang tua yang peduli pendidikan anak.',
    color: 'text-primary-600 bg-primary-50',
  },
]
</script>

<template>
  <SectionWrapper
    title="Kenapa Memilih PaudPedia?"
    subtitle="Kami menyediakan berbagai layanan untuk mendukung pendidikan anak usia dini yang berkualitas."
    bg="bg-surface"
  >
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="feature in features"
        :key="feature.title"
        class="group p-6 rounded-2xl border border-border bg-surface hover:shadow-card transition-all duration-300"
      >
        <div :class="['inline-flex items-center justify-center w-12 h-12 rounded-xl mb-4', feature.color]">
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
  </SectionWrapper>
</template>
