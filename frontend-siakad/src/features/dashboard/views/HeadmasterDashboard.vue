<script setup lang="ts">
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'

const authStore = useAuthStore()
const schoolStore = useSchoolStore()

const stats = [
  { 
    name: 'Siswa', 
    value: schoolStore.currentSchool?.total_students || 0, 
    icon: 'student', 
    color: 'bg-indigo-50 text-indigo-600 border-indigo-100',
    to: '/students'
  },
  { 
    name: 'Guru', 
    value: schoolStore.currentSchool?.total_teachers || 0, 
    icon: 'teacher', 
    color: 'bg-emerald-50 text-emerald-600 border-emerald-100',
    to: '/teachers'
  },
  { 
    name: 'Kelas', 
    value: schoolStore.currentSchool?.total_classes || 0, 
    icon: 'class', 
    color: 'bg-amber-50 text-amber-600 border-amber-100',
    to: '/classes'
  },
]

const icons: Record<string, string> = {
  student: 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
  teacher: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
  class: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
}
</script>

<template>
  <div class="space-y-8 animate-fade-in">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Halo, {{ authStore.userName }} 👋</h1>
        <p class="text-slate-500 font-medium">Selamat datang di panel manajemen {{ schoolStore.currentSchool?.name }}</p>
      </div>
      <div v-if="schoolStore.isPro" class="flex items-center gap-2.5 px-4 py-2 rounded-xl bg-amber-50 text-amber-700 text-xs font-black border border-amber-200 shadow-sm shadow-amber-900/5 uppercase tracking-widest">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
        </svg>
        SIAKAD PRO
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <RouterLink 
        v-for="stat in stats" 
        :key="stat.name" 
        :to="stat.to"
        class="group"
      >
        <BaseCard class="p-6 h-full border-slate-100 hover:border-primary-400 hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-300 cursor-pointer overflow-hidden relative">
          <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
              <path :d="icons[stat.icon]" />
            </svg>
          </div>
          
          <div class="flex items-center gap-5 relative z-10">
            <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center border shadow-sm transition-transform group-hover:scale-110', stat.color]">
              <svg class="w-7 h-7 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" :d="icons[stat.icon]" />
              </svg>
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">{{ stat.name }}</p>
              <div class="flex items-baseline gap-1">
                <p class="text-3xl font-black text-slate-900 tracking-tighter">{{ stat.value }}</p>
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mb-1.5" v-if="stat.value > 0"></div>
              </div>
            </div>
          </div>
        </BaseCard>
      </RouterLink>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Quick Actions -->
      <div class="lg:col-span-2 space-y-5">
        <h2 class="text-lg font-bold text-heading">Aksi Cepat</h2>
        <div class="grid grid-cols-2 gap-4">
          <RouterLink to="/teachers/create" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold">Tambah Guru</span>
          </RouterLink>
          <RouterLink to="/school/profile" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold">Profil Sekolah</span>
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-action {
  @apply flex flex-col items-center justify-center p-4 rounded-2xl bg-surface border border-border-muted hover:border-primary-200 hover:shadow-lg hover:shadow-primary-600/5 transition-all gap-2 text-sm font-medium text-body;
}
.action-icon {
  @apply w-10 h-10 rounded-xl flex items-center justify-center;
}
</style>
