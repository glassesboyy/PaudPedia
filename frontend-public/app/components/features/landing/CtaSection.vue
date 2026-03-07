<script setup lang="ts">
/**
 * CtaSection — SIAKAD SaaS conversion section for the landing page.
 *
 * Encourages schools to register for the SIAKAD system.
 * Fully static content. Uses design system tokens exclusively.
 */

const benefits = [
  { icon: 'lucide:school', text: 'Manajemen data siswa & guru' },
  { icon: 'lucide:calendar-check', text: 'Absensi & jadwal otomatis' },
  { icon: 'lucide:bar-chart-3', text: 'Laporan perkembangan anak' },
  { icon: 'lucide:shield-check', text: 'Data aman & privasi terjamin' },
]
</script>

<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800">
    <div class="container py-16 sm:py-24 lg:py-28 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
        <!-- Left: Content -->
        <div class="text-center lg:text-left">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/10 mb-6">
            <Icon name="lucide:layout-dashboard" class="w-3.5 h-3.5 text-primary-200" />
            <span class="text-xs font-semibold text-primary-100 uppercase tracking-wider">Sistem SIAKAD</span>
          </div>

          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight">
            Kelola Sekolah PAUD Anda dengan
            <span class="text-secondary-300">Sistem Digital Terpadu</span>
          </h2>

          <p class="mt-4 sm:mt-5 text-base sm:text-lg text-primary-100 leading-relaxed max-w-lg mx-auto lg:mx-0">
            SIAKAD PaudPedia adalah layanan SaaS untuk manajemen sekolah PAUD.
            Daftarkan sekolah Anda dan nikmati kemudahan administrasi yang modern dan efisien.
          </p>

          <!-- CTA Buttons -->
          <div class="mt-8 flex flex-wrap gap-4 justify-center lg:justify-start">
            <NuxtLink to="/auth/register">
              <UButton variant="secondary" size="lg" class="px-8 shadow-strong">
                Daftarkan Sekolah Anda
                <Icon name="lucide:arrow-right" class="w-4 h-4 ml-2" />
              </UButton>
            </NuxtLink>
            <NuxtLink to="/about">
              <UButton variant="ghost" size="lg" class="!text-white hover:!bg-white/10 px-8">
                Pelajari Lebih Lanjut
              </UButton>
            </NuxtLink>
          </div>
        </div>

        <!-- Right: Benefits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-for="benefit in benefits"
            :key="benefit.text"
            class="flex items-start gap-3 p-5 rounded-2xl bg-white/[0.08] border border-white/[0.08] backdrop-blur-sm"
          >
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mt-0.5">
              <Icon :name="benefit.icon" class="w-5 h-5 text-secondary-300" />
            </div>
            <span class="text-sm sm:text-base text-white font-medium leading-snug">
              {{ benefit.text }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Decorative circles -->
    <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-white/[0.04]" />
    <div class="absolute -bottom-20 -right-20 w-72 h-72 rounded-full bg-white/[0.04]" />
    <div class="absolute top-1/3 left-[15%] w-24 h-24 rounded-full bg-white/[0.03]" />
    <div class="absolute bottom-1/4 right-[20%] w-16 h-16 rounded-full bg-white/[0.03]" />
  </section>
</template>