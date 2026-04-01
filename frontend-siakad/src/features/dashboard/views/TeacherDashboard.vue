<script setup lang="ts">
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'

const authStore = useAuthStore()
const schoolStore = useSchoolStore()
</script>

<template>
  <div class="space-y-8">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-heading">Halo, {{ authStore.userName }} 👋</h1>
        <p class="text-muted">Selamat datang di Panel Guru {{ schoolStore.currentSchool?.name }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <BaseCard class="p-10 text-center flex flex-col items-center justify-center gap-4">
        <svg class="w-16 h-16 text-primary-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <h3 class="text-xl font-bold text-heading">Mulai Kegiatan Mengajar</h3>
        <p class="text-muted max-w-sm">Akses materi pembelajaran, kelola absensi, dan beri penilaian pada hari ini.</p>
        <div class="flex gap-3">
          <RouterLink to="/attendance" class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm">
            Input Absensi
          </RouterLink>
          <RouterLink to="/students" class="btn-outline px-6 py-2.5 rounded-xl font-bold text-sm">
            Daftar Siswa
          </RouterLink>
        </div>
      </BaseCard>

      <BaseCard class="p-6">
        <h2 class="text-lg font-bold text-heading mb-4">Jadwal Hari Ini</h2>
        <div class="py-12 text-center text-muted italic">
          Fitur jadwal mengajar segera hadir.
        </div>
      </BaseCard>
    </div>
  </div>
</template>
