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
        <Icon name="lucide:book-open" stroke-width="1" class="w-16 h-16 text-primary-200" />
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
