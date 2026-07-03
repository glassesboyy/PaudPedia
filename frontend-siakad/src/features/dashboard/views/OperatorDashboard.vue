<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'
import Icon from '@/components/ui/Icon/Icon.vue'
import { usePageCopy } from '@/utils/copy-helper'

const router = useRouter()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const copy = computed(() => getCopy('dashboard'))

const stats = computed(() => [
  { 
    name: 'Siswa', 
    value: schoolStore.currentSchool?.total_students || 0, 
    icon: 'lucide:backpack', 
    color: 'bg-indigo-50 text-indigo-600 border-indigo-100',
    to: '/students'
  },
  { 
    name: 'Guru', 
    value: schoolStore.currentSchool?.total_teachers || 0, 
    icon: 'lucide:graduation-cap', 
    color: 'bg-emerald-50 text-emerald-600 border-emerald-100',
    to: '/teachers'
  },
  { 
    name: 'Kelas', 
    value: schoolStore.currentSchool?.total_classes || 0, 
    icon: 'lucide:school', 
    color: 'bg-amber-50 text-amber-600 border-amber-100',
    to: '/classes'
  },
  { 
    name: 'Wali Murid', 
    value: schoolStore.currentSchool?.total_parents || 0, 
    icon: 'lucide:users-2', 
    color: 'bg-rose-50 text-rose-600 border-rose-100',
    to: '/parents'
  },
])
</script>

<template>
  <div class="space-y-8 animate-fade-in pb-10">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Halo, {{ authStore.userName }}</h1>
        <p class="text-slate-500 font-medium">{{ copy.subtitle || 'Panel Operasional & Administrasi Harian Sekolah' }}</p>
      </div>
      <div class="flex items-center gap-3">
        <div v-if="schoolStore.isPro" class="flex items-center gap-2.5 px-4 py-2 rounded-xl bg-primary-100 text-primary-800 text-xs font-black border border-primary-200 shadow-sm shadow-primary-900/5 uppercase tracking-widest">
          <Icon name="lucide:star" fill="currentColor" class="w-4 h-4" />
          SIAKAD PRO
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="space-y-4 mt-6">
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
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
        <RouterLink to="/assessments/settings" class="dashboard-action group">
          <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
            <Icon name="lucide:settings" class="w-5 h-5" stroke-width="2" />
          </div>
          <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Master Penilaian</span>
        </RouterLink>
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
        <BaseCard class="p-6 h-full border-slate-100 hover:border-primary-400 hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-300 cursor-pointer overflow-hidden relative bg-white">
          <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
            <Icon :name="stat.icon" class="w-24 h-24" />
          </div>
          
          <div class="flex items-center gap-5 relative z-10">
            <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center border shadow-sm transition-transform group-hover:scale-110', stat.color]">
              <Icon :name="stat.icon" class="w-7 h-7 shrink-0" stroke-width="1.5" />
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

    <!-- Modul Operasional Keuangan -->
    <div v-if="schoolStore.isPro" class="space-y-4">
      <h2 class="text-lg font-bold text-heading mt-6">Modul Operasional Keuangan</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <BaseCard class="p-6 border-none shadow-lg hover:shadow-xl transition-shadow cursor-pointer bg-white" @click="router.push({ name: 'SppManagement' })">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-5">
              <div class="w-16 h-16 rounded-2xl bg-primary-50 flex items-center justify-center border border-primary-100">
                <Icon name="lucide:credit-card" class="w-8 h-8 text-primary-600" />
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Administrasi Keuangan</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">Manajemen SPP</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Buat tagihan baru & verifikasi pembayaran SPP siswa.</p>
              </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
              <Icon name="lucide:chevron-right" class="w-5 h-5" />
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6 border-none shadow-lg hover:shadow-xl transition-shadow cursor-pointer bg-white" @click="router.push({ name: 'SavingsManagement' })">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-5">
              <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center border border-emerald-100">
                <Icon name="lucide:piggy-bank" class="w-8 h-8 text-emerald-600" />
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Administrasi Keuangan</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">Manajemen Tabungan</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Catat transaksi setoran dan penarikan tabungan siswa.</p>
              </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
              <Icon name="lucide:chevron-right" class="w-5 h-5" />
            </div>
          </div>
        </BaseCard>
      </div>
    </div>
  </div>
</template>

<style scoped lang="postcss">
.dashboard-action {
  @apply flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-slate-100 hover:border-primary-200 hover:shadow-lg hover:shadow-primary-600/5 transition-all gap-2 text-sm font-medium text-slate-600;
}
.action-icon {
  @apply w-10 h-10 rounded-xl flex items-center justify-center;
}
</style>
