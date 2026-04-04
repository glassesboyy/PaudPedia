<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import { studentService } from '@/features/students/services/student.service'
import type { Student } from '@/types'
import BaseCard from '@/components/ui/Card/Card.vue'
import { usePageCopy } from '@/utils/copy-helper'

const authStore = useAuthStore()
const schoolStore = useSchoolStore()
const router = useRouter()

const myChildren = ref<Student[]>([])
const isLoadingChildren = ref(false)

const { getCopy } = usePageCopy()
const copy = computed(() => getCopy('dashboard'))

const stats = computed(() => [
  { 
    name: 'Anak Saya', 
    value: myChildren.value.length, 
    icon: 'lucide:smile', 
    color: 'bg-indigo-50 text-indigo-600 border-indigo-100',
    to: '/children'
  },
])

onMounted(async () => {
  if (schoolStore.currentSchoolId && authStore.user?.id) {
    fetchMyChildren()
  }
})

async function fetchMyChildren() {
  isLoadingChildren.value = true
  try {
    const response = await studentService.getStudents(schoolStore.currentSchoolId!, {
      parent_user_id: authStore.user?.id,
      per_page: 10
    })
    myChildren.value = response.data
  } catch {
    myChildren.value = []
  } finally {
    isLoadingChildren.value = false
  }
}
</script>

<template>
  <div class="space-y-8 animate-fade-in">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ copy.title }}</h1>
        <p class="text-slate-500 font-medium tracking-tight">{{ copy.subtitle }}</p>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="flex gap-6">
      <RouterLink 
        v-for="stat in stats" 
        :key="stat.name" 
        :to="stat.to"
        class="group w-full sm:w-72"
      >
        <BaseCard class="p-6 h-full border-slate-100 hover:border-primary-400 hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-300 cursor-pointer overflow-hidden relative">
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

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- My Children List -->
      <div class="lg:col-span-3 space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-black text-heading tracking-tight">Daftar Buah Hati</h2>
          <RouterLink to="/children" class="text-xs font-black text-primary-600 uppercase tracking-widest flex items-center gap-1 group">
            Lihat Semua
            <Icon name="lucide:arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
          </RouterLink>
        </div>
        
        <div v-if="isLoadingChildren" class="space-y-4">
          <BaseCard v-for="i in 2" :key="i" class="p-6 animate-pulse bg-slate-50 border-none h-24 flex items-center gap-6 rounded-[1.5rem] shadow-sm">
            <Skeleton width="4rem" height="4rem" class="rounded-2xl shrink-0" />
            <div class="flex-1 space-y-3"><Skeleton width="40%" height="1.25rem" /><Skeleton width="20%" height="0.75rem" /></div>
          </BaseCard>
        </div>
        
        <div v-else-if="myChildren.length === 0" class="p-16 text-center bg-white border-2 border-dashed border-slate-100 rounded-[2.5rem] shadow-sm">
          <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-200 mx-auto mb-6">
            <Icon name="lucide:smile" class="w-10 h-10" />
          </div>
          <h3 class="text-lg font-bold text-heading mb-2">Belum Ada Data Anak</h3>
          <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto">Data anak Anda belum terdaftar di sistem. Sinkronisasi data mungkin sedang berlangsung di pusat data sekolah.</p>
        </div>
        
        <div v-else class="grid grid-cols-1 gap-4">
          <BaseCard 
            v-for="child in myChildren" 
            :key="child.id" 
            class="p-4 border-none shadow-lg shadow-primary-900/5 hover:shadow-xl hover:shadow-primary-900/10 cursor-pointer group transition-all duration-300 rounded-[1.5rem] bg-white border border-transparent hover:border-primary-100"
            @click="router.push({ name: 'StudentDetail', params: { id: child.id } })"
          >
            <div class="flex items-center gap-6">
              <!-- Visual -->
              <div class="w-16 h-16 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200 shadow-sm group-hover:scale-105 transition-transform duration-500">
                <img v-if="child.photo_url" :src="child.photo_url" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                  <Icon name="lucide:user" class="w-8 h-8" />
                </div>
              </div>

              <!-- Main Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <h4 class="font-black text-heading group-hover:text-primary-700 transition-colors text-lg truncate leading-tight">{{ child.name }}</h4>
                  <span class="px-2 py-0.5 rounded-lg bg-pink-50 text-pink-600 text-[10px] font-black uppercase tracking-widest border border-pink-100 shrink-0">
                    {{ child.status }}
                  </span>
                </div>
                <div class="flex items-center flex-wrap gap-4">
                  <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-primary-500"></div>
                    <span class="text-xs font-bold text-slate-600 uppercase">{{ child.class?.name || 'Belum Ada Kelas' }}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <Icon name="lucide:fingerprint" class="w-3.5 h-3.5 text-slate-400" />
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NISN: {{ child.nisn || '-' }}</span>
                  </div>
                </div>
              </div>

              <!-- Action Indicator -->
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
          <RouterLink to="/children" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:heart" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Informasi Anak</span>
          </RouterLink>
          <RouterLink to="/children/attendance" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:user-check" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Presensi</span>
          </RouterLink>
          <RouterLink to="/children/assessment" class="dashboard-action group">
            <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
              <Icon name="lucide:file-text" class="w-5 h-5" stroke-width="2" />
            </div>
            <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Rapor</span>
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
