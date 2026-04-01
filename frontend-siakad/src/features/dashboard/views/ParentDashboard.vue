<script setup lang="ts">
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'

const authStore = useAuthStore()
const schoolStore = useSchoolStore()
</script>

<template>
  <div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 text-center md:text-left">
      <div>
        <h1 class="text-2xl font-bold text-heading">Halo, Bapak/Ibu {{ authStore.userName }} 👋</h1>
        <p class="text-muted">Pantau perkembangan buah hati Anda di {{ schoolStore.currentSchool?.name }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
      <BaseCard class="p-8 text-center flex flex-col items-center justify-center gap-4 bg-primary-100/10 border-primary-200 border-dashed">
        <Icon name="lucide:smile" stroke-width="1" class="w-20 h-20 text-primary-200" />
        <div>
          <h3 class="text-xl font-bold text-heading">Monitoring Perkembangan Anak</h3>
          <p class="text-muted mt-2">Lihat capaian harian, rapor bulanan, dan jadwal sekolah secara langsung.</p>
        </div>
        <div class="flex flex-col gap-2 w-full mt-4">
          <RouterLink to="/children" class="btn-primary w-full py-2.5 rounded-xl font-bold text-sm">
            Lihat Daftar Anak
          </RouterLink>
          <div class="p-4 bg-warning-50 border border-warning-200 rounded-xl text-xs text-warning-800 flex items-center gap-3">
             <div class="w-2 h-2 rounded-full bg-warning-500 animate-pulse"></div>
             Fitur absensi harian segera dirilis untuk Bapak/Ibu.
          </div>
        </div>
      </BaseCard>

      <div class="space-y-6">
        <BaseCard class="p-6">
           <h4 class="text-sm font-bold text-heading uppercase tracking-wider mb-4">Pengumuman Terakhir</h4>
           <div class="p-4 bg-surface-muted rounded-xl text-sm italic text-muted">
              Belum ada pengumuman terbaru dari sekolah.
           </div>
        </BaseCard>
        
        <BaseCard class="p-6">
           <h4 class="text-sm font-bold text-heading uppercase tracking-wider mb-4">Kalender Penting</h4>
           <p class="text-xs text-muted">Akan ditayangkan agenda sekolah mendatang.</p>
        </BaseCard>
      </div>
    </div>
  </div>
</template>
