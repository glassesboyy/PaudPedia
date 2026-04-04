<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'

const authStore = useAuthStore()
const schoolStore = useSchoolStore()

const stats = computed(() => [
  { 
    name: 'Siswa', 
    value: schoolStore.currentSchool?.total_students || 0, 
    icon: 'student', 
    color: 'bg-indigo-50 text-indigo-600 border-indigo-100',
    to: '/students'
  },
  { 
    name: 'Orang Tua', 
    value: schoolStore.currentSchool?.total_parents || 0, 
    icon: 'parent', 
    color: 'bg-rose-50 text-rose-600 border-rose-100',
    to: '/parents'
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
])

const icons: Record<string, string> = {
  student: 'lucide:backpack',
  parent: 'lucide:users-2',
  teacher: 'lucide:graduation-cap',
  class: 'lucide:school',
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
        <Icon name="lucide:star" fill="currentColor" class="w-4 h-4" />
        SIAKAD PRO
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <RouterLink 
        v-for="stat in stats" 
        :key="stat.name" 
        :to="stat.to"
        class="group"
      >
        <BaseCard class="p-6 h-full border-slate-100 hover:border-primary-400 hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-300 cursor-pointer overflow-hidden relative">
          <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
            <Icon :name="icons[stat.icon]" class="w-24 h-24" />
          </div>
          
          <div class="flex items-center gap-5 relative z-10">
            <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center border shadow-sm transition-transform group-hover:scale-110', stat.color]">
              <Icon :name="icons[stat.icon]" class="w-7 h-7 shrink-0" stroke-width="1.5" />
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
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <RouterLink to="/students/create" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:user-plus" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Tambah Siswa</span>
          </RouterLink>
          <RouterLink to="/teachers/create" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:graduation-cap" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Tambah Guru</span>
          </RouterLink>
          <RouterLink to="/parents/create" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:heart-handshake" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Tambah Wali</span>
          </RouterLink>
          <RouterLink to="/classes/create" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:door-open" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Tambah Kelas</span>
          </RouterLink>
          <RouterLink to="/school/profile" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:settings" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Setting</span>
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
