<script setup lang="ts">
/**
 * AuthLayout — Split-screen layout for authentication pages.
 * Left: Branding/illustration panel (hidden on mobile)
 * Right: Auth form content via slot
 */
import { RouterView } from 'vue-router'
</script>

<template>
  <div class="min-h-screen flex bg-background">
    <!-- Left panel — Branding (hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900">
      <div class="relative z-10 flex flex-col justify-between p-12 w-full">
        <!-- Logo -->
        <div class="flex items-center">
            <span class="text-xl font-bold text-white">PaudPedia</span>
            <span class="text-xs font-semibold text-primary-200 ml-2 bg-white/10 px-2 py-0.5 rounded">SIAKAD</span>
        </div>

        <!-- Center content -->
        <div class="flex-1 flex flex-col justify-center max-w-md">
          <h1 class="text-3xl xl:text-4xl font-bold text-white leading-tight">
            Kelola Sekolah PAUD dengan
            <span class="text-secondary-300">Sistem Digital Terpadu</span>
          </h1>
          <p class="mt-5 text-primary-100 text-lg leading-relaxed">
            SIAKAD PaudPedia membantu Anda mengelola administrasi sekolah, 
            data siswa, absensi, dan laporan perkembangan anak secara efisien.
          </p>

          <!-- Feature list -->
          <div class="mt-8 space-y-4">
            <div v-for="feature in [
              'Manajemen data siswa & guru',
              'Absensi & laporan otomatis',
              'Monitoring perkembangan anak',
              'Data aman & terenkripsi',
            ]" :key="feature" class="flex items-center gap-3">
              <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <Icon name="lucide:check" stroke-width="3" class="w-3.5 h-3.5 text-secondary-300" />
              </div>
              <span class="text-primary-100 text-sm">{{ feature }}</span>
            </div>
          </div>
        </div>

        <!-- Bottom -->
        <p class="text-primary-200 text-sm">
          &copy; {{ new Date().getFullYear() }} PaudPedia. Platform Pendidikan PAUD.
        </p>
      </div>

      <!-- Decorative circles -->
      <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/[0.04]" />
      <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-white/[0.04]" />
      <div class="absolute top-1/3 right-[10%] w-32 h-32 rounded-full bg-white/[0.03]" />
    </div>

    <!-- Right panel — Form content -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8 lg:p-12">
      <div class="w-full max-w-md animate-fade-in">
        <!-- Mobile logo (visible only on small screens) -->
        <div class="lg:hidden flex items-center justify-center gap-2.5 mb-8">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-sm">
            <Icon name="lucide:school" class="w-6 h-6 text-white" />
          </div>
          <div>
            <span class="text-xl font-bold text-heading">PaudPedia</span>
            <span class="text-xs font-semibold text-primary-600 ml-1.5 bg-primary-50 px-1.5 py-0.5 rounded">SIAKAD</span>
          </div>
        </div>

        <RouterView />
      </div>
    </div>
  </div>
</template>
