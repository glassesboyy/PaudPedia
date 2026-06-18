<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import { classService } from '@/features/classes/services/class.service'
import type { ClassRoom } from '@/types'
import BaseCard from '@/components/ui/Card/Card.vue'
import { usePageCopy } from '@/utils/copy-helper'

const authStore = useAuthStore()
const schoolStore = useSchoolStore()
const router = useRouter()

const myClasses = ref<ClassRoom[]>([])
const isLoadingClasses = ref(false)

const { getCopy } = usePageCopy()
const copy = computed(() => getCopy('dashboard'))
const isHeadmaster = computed(() => schoolStore.isHeadmaster)

const stats = computed(() => [
  { 
    name: 'Total Siswa', 
    value: schoolStore.currentSchool?.total_students || 0,
    icon: 'lucide:backpack', 
    color: 'bg-indigo-50 text-indigo-600 border-indigo-100',
    to: '/students'
  },
  { 
    name: 'Kelas yang Diampu', 
    value: myClasses.value.length, 
    icon: 'lucide:school', 
    color: 'bg-amber-50 text-amber-600 border-amber-100',
    to: '/classes'
  },
])

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    fetchAllClasses()
  }
})

async function fetchAllClasses() {
  isLoadingClasses.value = true
  try {
    const response = await classService.getClasses(schoolStore.currentSchoolId!, {
      per_page: 5
    })
    myClasses.value = response.data
  } catch {
    myClasses.value = []
  } finally {
    isLoadingClasses.value = false
  }
}
</script>

<template>
  <div class="space-y-8 animate-fade-in">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Halo, {{ authStore.userName }}</h1>
        <p class="text-slate-500 font-medium tracking-tight">{{ copy.subtitle }}</p>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <RouterLink 
        v-for="stat in stats" 
        :key="stat.name" 
        :to="stat.to"
        class="group"
      >
        <BaseCard class="p-8 border-none shadow-xl shadow-primary-900/5 hover:shadow-2xl hover:shadow-primary-900/10 transition-all duration-300 cursor-pointer overflow-hidden relative bg-white">
          <div class="absolute -right-6 -bottom-6 opacity-[0.05] group-hover:opacity-[0.1] transition-opacity duration-500 transform group-hover:scale-110">
            <Icon :name="stat.icon" class="w-32 h-32" />
          </div>
          
          <div class="flex items-center gap-6 relative z-10 font-bold">
            <div :class="['w-16 h-16 rounded-[1.5rem] flex items-center justify-center border shadow-sm transition-all duration-500 group-hover:rotate-12 group-hover:scale-110', stat.color]">
              <Icon :name="stat.icon" class="w-8 h-8 shrink-0" stroke-width="2" />
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em] mb-1">{{ stat.name }}</p>
              <div class="flex items-baseline gap-2">
                <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ stat.value }}</p>
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mb-1.5" v-if="stat.value > 0"></div>
              </div>
            </div>
          </div>
        </BaseCard>
      </RouterLink>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Classes (Elongated) -->
      <div class="lg:col-span-3 space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-black text-heading tracking-tight">Daftar Kelas</h2>
          <RouterLink to="/classes" class="text-xs font-black text-primary-600 uppercase tracking-widest hover:text-primary-700 flex items-center gap-1.5 group">
            Lihat Semua
            <Icon name="lucide:arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
          </RouterLink>
        </div>
        
        <div v-if="isLoadingClasses" class="space-y-4">
          <BaseCard v-for="i in 3" :key="i" class="p-6 animate-pulse bg-slate-50 border-none h-24 flex items-center gap-6 rounded-[1.5rem]">
            <Skeleton width="3rem" height="3rem" class="rounded-xl" />
            <div class="flex-1 space-y-3"><Skeleton width="40%" height="1rem" /><Skeleton width="20%" height="0.6rem" /></div>
          </BaseCard>
        </div>
        
        <div v-else-if="myClasses.length === 0" class="p-16 text-center bg-white border-2 border-dashed border-slate-100 rounded-[2.5rem]">
          <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-200 mx-auto mb-6">
            <Icon name="lucide:layout-list" class="w-10 h-10" />
          </div>
          <h3 class="text-lg font-bold text-heading mb-2">Belum Ada Kelas</h3>
          <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto">Unit sekolah ini belum memiliki daftar kelas.</p>
        </div>
        
        <div v-else class="space-y-4">
          <BaseCard 
            v-for="c in myClasses" 
            :key="c.id" 
            class="p-4 border-none shadow-lg shadow-primary-900/5 hover:shadow-xl hover:shadow-primary-900/10 cursor-pointer group transition-all duration-300 rounded-[1.5rem] bg-white border border-transparent hover:border-primary-100"
            @click="router.push({ name: 'ClassDetail', params: { id: c.id } })"
          >
            <div class="flex items-center gap-6">
              <!-- Icon/Visual -->
              <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white transition-all duration-500 shadow-sm border border-primary-100 group-hover:border-primary-600">
                <Icon name="lucide:users" class="w-7 h-7" stroke-width="2" />
              </div>
              
              <!-- Info Middle -->
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                   <h4 class="text-lg font-black text-heading group-hover:text-primary-700 transition-colors">{{ c.name }}</h4>
                   <span class="px-2 py-0.5 rounded-lg bg-secondary-50 text-secondary-700 text-[10px] font-black uppercase tracking-widest border border-secondary-100">
                    {{ c.level || 'TA' }}
                  </span>
                </div>
                <div class="flex items-center gap-4">
                   <div class="flex items-center gap-1.5">
                     <Icon name="lucide:user-2" class="w-3.5 h-3.5 text-slate-400" />
                     <span class="text-xs font-bold text-slate-500 uppercase">{{ c.current_students || 0 }} Siswa Terdaftar</span>
                   </div>
                   <div class="flex items-center gap-1.5">
                     <Icon name="lucide:calendar-days" class="w-3.5 h-3.5 text-slate-400" />
                     <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ c.academic_year }}</span>
                   </div>
                </div>
              </div>

              <!-- Action button (only shows on hover on larger screen or always visible) -->
              <div class="hidden sm:flex items-center gap-2 pr-4 text-xs font-black text-slate-300 uppercase tracking-widest group-hover:text-primary-600 group-hover:gap-4 transition-all">
                Detail
                <Icon name="lucide:chevron-right" class="w-5 h-5" />
              </div>
            </div>
          </BaseCard>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="space-y-5">
        <h2 class="text-lg font-bold text-heading">Aksi Cepat</h2>
        <div class="grid grid-cols-1 gap-3">
          <RouterLink to="/students" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:users" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Data Siswa</span>
          </RouterLink>
          <RouterLink to="/attendance" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:calendar-check" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Absensi</span>
          </RouterLink>
          <RouterLink to="/assessment" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:clipboard-check" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Penilaian</span>
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-action {
  @apply flex items-center p-4 rounded-2xl bg-white border border-slate-100 hover:border-primary-200 hover:shadow-lg hover:shadow-primary-600/5 transition-all gap-4 text-sm font-medium text-slate-600;
}
.action-icon {
  @apply w-10 h-10 rounded-xl flex items-center justify-center shrink-0;
}
</style>
