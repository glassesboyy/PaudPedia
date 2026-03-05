<script setup lang="ts">
/**
 * About Us Page
 * Route: /about
 *
 * FR-PP-10: Visi, Misi, Tim — Static content.
 */
useSeo({
  title: 'Tentang Kami',
  description: 'Pelajari lebih lanjut tentang PaudPedia, visi, misi, dan tim di balik platform pendidikan anak usia dini.',
})

const teamMembers = [
  {
    name: 'Dr. Sari Dewi, M.Pd',
    role: 'Founder & CEO',
    description: 'Pakar pendidikan anak usia dini dengan pengalaman lebih dari 15 tahun di bidang kurikulum PAUD.',
    icon: 'lucide:user',
  },
  {
    name: 'Ahmad Rizki, S.Kom',
    role: 'CTO',
    description: 'Teknolog berpengalaman yang passionate dalam mengembangkan solusi edukasi berbasis teknologi.',
    icon: 'lucide:user',
  },
  {
    name: 'Rina Handayani, M.Psi',
    role: 'Head of Content',
    description: 'Psikolog anak yang memastikan seluruh konten sesuai dengan tahap perkembangan anak usia dini.',
    icon: 'lucide:user',
  },
  {
    name: 'Budi Santoso, S.Pd',
    role: 'Head of Mentor',
    description: 'Mantan kepala sekolah PAUD yang kini mengelola jaringan mentor berkualitas di PaudPedia.',
    icon: 'lucide:user',
  },
]

const milestones = [
  { year: '2024', event: 'PaudPedia didirikan dengan visi memajukan pendidikan anak usia dini di Indonesia.' },
  { year: '2025', event: 'Peluncuran platform e-learning dengan 50+ kursus dan 20+ mentor berpengalaman.' },
  { year: '2026', event: 'Ekspansi ke marketplace produk edukasi dan webinar interaktif untuk pendidik PAUD.' },
]
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-gradient-to-br from-primary-50 to-surface">
      <div class="container py-14 sm:py-20 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-heading">Tentang PaudPedia</h1>
        <p class="mt-4 text-base text-body max-w-2xl mx-auto leading-relaxed">
          PaudPedia adalah platform e-learning dan marketplace yang didedikasikan untuk
          meningkatkan kualitas pendidikan anak usia dini di Indonesia.
        </p>
      </div>
    </section>

    <!-- Visi & Misi -->
    <SectionWrapper title="Visi & Misi" bg="bg-surface">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
        <div class="p-6 rounded-2xl border border-border bg-primary-50/50">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary-100 mb-4">
            <Icon name="lucide:eye" class="w-6 h-6 text-primary-600" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-3">Visi</h3>
          <p class="text-sm text-body leading-relaxed">
            Menjadi platform pendidikan anak usia dini terdepan di Indonesia yang membantu
            menciptakan generasi emas melalui pendidikan berkualitas, aksesibel, dan inovatif.
          </p>
        </div>
        <div class="p-6 rounded-2xl border border-border bg-secondary-50/50">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-secondary-100 mb-4">
            <Icon name="lucide:target" class="w-6 h-6 text-secondary-600" />
          </div>
          <h3 class="text-lg font-semibold text-heading mb-3">Misi</h3>
          <ul class="text-sm text-body leading-relaxed space-y-2">
            <li class="flex items-start gap-2">
              <Icon name="lucide:check-circle" class="w-4 h-4 text-success-500 shrink-0 mt-0.5" />
              <span>Menyediakan materi pembelajaran PAUD yang berkualitas dan terstruktur.</span>
            </li>
            <li class="flex items-start gap-2">
              <Icon name="lucide:check-circle" class="w-4 h-4 text-success-500 shrink-0 mt-0.5" />
              <span>Memfasilitasi pendidik dan orang tua dengan akses ke sumber belajar yang terjangkau.</span>
            </li>
            <li class="flex items-start gap-2">
              <Icon name="lucide:check-circle" class="w-4 h-4 text-success-500 shrink-0 mt-0.5" />
              <span>Membangun komunitas pendidik PAUD yang saling mendukung dan berkolaborasi.</span>
            </li>
            <li class="flex items-start gap-2">
              <Icon name="lucide:check-circle" class="w-4 h-4 text-success-500 shrink-0 mt-0.5" />
              <span>Menghadirkan inovasi dalam metode pembelajaran anak usia dini melalui teknologi.</span>
            </li>
          </ul>
        </div>
      </div>
    </SectionWrapper>

    <!-- Timeline -->
    <SectionWrapper title="Perjalanan Kami" bg="bg-surface-muted" compact>
      <div class="max-w-2xl mx-auto space-y-6">
        <div
          v-for="milestone in milestones"
          :key="milestone.year"
          class="flex gap-4 items-start"
        >
          <div class="shrink-0 w-16 h-16 rounded-2xl bg-primary-100 flex items-center justify-center">
            <span class="text-sm font-bold text-primary-700">{{ milestone.year }}</span>
          </div>
          <div class="pt-2">
            <p class="text-sm text-body leading-relaxed">{{ milestone.event }}</p>
          </div>
        </div>
      </div>
    </SectionWrapper>

    <!-- Tim -->
    <SectionWrapper
      title="Tim Kami"
      subtitle="Orang-orang berbakat di balik PaudPedia."
      bg="bg-surface"
    >
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="member in teamMembers"
          :key="member.name"
          class="text-center p-6 rounded-2xl border border-border bg-surface"
        >
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 mb-4">
            <Icon :name="member.icon" class="w-8 h-8 text-primary-600" />
          </div>
          <h3 class="text-sm font-semibold text-heading">{{ member.name }}</h3>
          <p class="text-xs text-primary-600 font-medium mt-1">{{ member.role }}</p>
          <p class="text-xs text-body mt-2 leading-relaxed">{{ member.description }}</p>
        </div>
      </div>
    </SectionWrapper>

    <!-- CTA -->
    <CtaSection />
  </div>
</template>
