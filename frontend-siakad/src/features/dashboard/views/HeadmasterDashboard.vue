<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import { usePageCopy } from '@/utils/copy-helper'
import { dashboardService, type HeadmasterDashboardSummary } from '@/features/dashboard/services/dashboard.service'
import DashboardChart from '../components/DashboardChart.vue'
import { watch } from 'vue'

const router = useRouter()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()
const { getCopy } = usePageCopy()

const copy = computed(() => getCopy('dashboard'))

const isLoading = ref(true)
const dashboardData = ref<HeadmasterDashboardSummary | null>(null)

const currentYear = new Date().getFullYear()
const filterOptions = computed(() => {
  const opts = [
    { value: 'all', label: 'Semua Waktu (All Time)' }
  ]
  // Generate last 12 months for dropdown
  for (let i = 0; i < 12; i++) {
    const d = new Date(currentYear, new Date().getMonth() - i, 1)
    const val = `${d.getFullYear()}-${(d.getMonth() + 1).toString().padStart(2, '0')}`
    opts.push({ value: val, label: d.toLocaleString('id-ID', { month: 'long', year: 'numeric' }) })
  }
  return opts
})

const filterValue = ref(`${currentYear}-${(new Date().getMonth() + 1).toString().padStart(2, '0')}`)

// Default chart range: last 6 months to current month
const chartEnd = ref(`${currentYear}-${(new Date().getMonth() + 1).toString().padStart(2, '0')}`)
const past6Months = new Date(currentYear, new Date().getMonth() - 5, 1)
const chartStart = ref(`${past6Months.getFullYear()}-${(past6Months.getMonth() + 1).toString().padStart(2, '0')}`)

watch([filterValue, chartStart, chartEnd], async () => {
  if (schoolStore.isPro) {
    await fetchDashboard()
  }
})

onMounted(async () => {
  if (schoolStore.isPro) {
    await fetchDashboard()
  } else {
    isLoading.value = false
  }
})

async function fetchDashboard() {
  isLoading.value = true
  try {
    const res: any = await dashboardService.getHeadmasterSummary(schoolStore.currentSchoolId!, filterValue.value, chartStart.value, chartEnd.value)
    dashboardData.value = res.data
  } catch (error) {
    console.error('Failed to fetch dashboard summary', error)
  } finally {
    isLoading.value = false
  }
}

function formatCurrency(val: number): string {
  return 'Rp ' + val.toLocaleString('id-ID')
}

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
  <div class="space-y-8 animate-fade-in pb-10">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Halo, {{ authStore.userName }}</h1>
        <p class="text-slate-500 font-medium">{{ copy.subtitle }}</p>
      </div>
      <div class="flex items-center gap-3">
        <div v-if="schoolStore.isPro" class="flex items-center gap-2.5 px-4 py-2 rounded-xl bg-primary-100 text-primary-800 text-xs font-black border border-primary-200 shadow-sm shadow-primary-900/5 uppercase tracking-widest">
          <Icon name="lucide:star" fill="currentColor" class="w-4 h-4" />
          SIAKAD PRO
        </div>
      </div>
    </div>

    <!-- Quick Actions (Moved to Top) -->
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
        <RouterLink to="/school/profile" class="dashboard-action group">
          <div class="action-icon border border-slate-200 text-slate-600 group-hover:bg-primary-600 group-hover:text-white transition-all">
            <Icon name="lucide:settings" class="w-5 h-5" stroke-width="2" />
          </div>
          <span class="group-hover:text-primary-600 transition-colors font-bold whitespace-nowrap">Setting</span>
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

    <template v-if="schoolStore.isPro">
      <div v-if="isLoading" class="space-y-6">
        <BaseCard class="p-6"><Skeleton height="300px" /></BaseCard>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <BaseCard v-for="i in 3" :key="i" class="p-6"><Skeleton height="5rem" /></BaseCard>
        </div>
      </div>
      
      <template v-else-if="dashboardData">
        <!-- 12-Month Finance Chart -->
        <h2 class="text-lg font-bold text-heading mt-10 mb-4">Grafik Keuangan</h2>
        <BaseCard class="p-4 border-slate-200 shadow-lg mb-8">
          <DashboardChart 
            v-model:start="chartStart"
            v-model:end="chartEnd"
            :labels="dashboardData.chart_data.labels"
            :spp="dashboardData.chart_data.spp"
            :tabungan="dashboardData.chart_data.tabungan"
            :total="dashboardData.chart_data.total"
          />
        </BaseCard>

        <!-- Finance Summary Cards -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-10 mb-4">
          <h2 class="text-lg font-bold text-heading m-0">Ringkasan Keuangan {{ filterValue === 'all' ? '(Semua Waktu)' : '(Bulan Pilihan)' }}</h2>
          <select v-if="schoolStore.isPro" v-model="filterValue" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 shadow-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition-all cursor-pointer">
            <option v-for="opt in filterOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <BaseCard class="p-2 border-none shadow-lg hover:shadow-xl transition-shadow cursor-pointer" @click="router.push({ name: 'SppManagement' })">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center">
                <Icon name="lucide:credit-card" class="w-7 h-7 text-primary-500" />
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">SPP Terkumpul</p>
                <p class="text-xl font-black text-slate-900">{{ formatCurrency(dashboardData.finance_summary.spp_collected) }}</p>
                <p class="text-xs text-amber-600 font-medium">{{ formatCurrency(dashboardData.finance_summary.spp_pending) }} belum lunas</p>
              </div>
            </div>
          </BaseCard>

          <BaseCard class="p-2 border-none shadow-lg hover:shadow-xl transition-shadow cursor-pointer" @click="router.push({ name: 'SavingsManagement' })">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <Icon name="lucide:piggy-bank" class="w-7 h-7 text-emerald-500" />
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Tabungan</p>
                <p class="text-xl font-black text-slate-900">{{ formatCurrency(dashboardData.finance_summary.savings_balance) }}</p>
                <p class="text-xs text-slate-400 font-medium">Total keseluruhan siswa</p>
              </div>
            </div>
          </BaseCard>

          <BaseCard class="p-2 border-none shadow-lg">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center">
                <Icon name="lucide:bar-chart-3" class="w-7 h-7 text-violet-500" />
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Pemasukan</p>
                <p class="text-xl font-black text-slate-900">{{ formatCurrency(dashboardData.finance_summary.spp_collected + dashboardData.finance_summary.savings_balance) }}</p>
                <p class="text-xs text-slate-400 font-medium">SPP + Tabungan</p>
              </div>
            </div>
          </BaseCard>
        </div>

        <!-- Top 5 Panels -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Top Attendance -->
          <BaseCard class="p-0 flex flex-col h-full border-slate-200">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
              <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <Icon name="lucide:award" class="w-4 h-4 text-emerald-500" /> Kehadiran Terbaik
              </h3>
              <button @click="router.push('/attendance')" class="text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 px-2.5 py-1 rounded-md">Kelola</button>
            </div>
            <div class="p-0 flex-1 flex flex-col">
              <div v-if="dashboardData.best_attendance.length === 0" class="p-8 text-center text-slate-400 text-sm m-auto">
                Belum ada data kehadiran bulan ini
              </div>
              <div v-else class="divide-y divide-slate-100">
                <div v-for="(item, i) in dashboardData.best_attendance" :key="item.student_id" class="px-4 py-3 flex items-center justify-between hover:bg-slate-50 transition-colors">
                  <div class="flex items-center gap-3 min-w-0">
                    <div class="w-6 h-6 rounded-full bg-slate-100 text-[10px] font-black text-slate-500 flex items-center justify-center shrink-0">#{{ i + 1 }}</div>
                    <div class="min-w-0">
                      <p class="text-sm font-bold text-slate-800 truncate">{{ item.student_name }}</p>
                      <p class="text-[10px] text-slate-500 truncate">{{ item.class_name }}</p>
                    </div>
                  </div>
                  <div class="text-right shrink-0">
                    <p class="text-sm font-black text-emerald-600">{{ item.present_count }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Kehadiran</p>
                  </div>
                </div>
              </div>
            </div>
          </BaseCard>

          <!-- SPP Arrears -->
          <BaseCard class="p-0 flex flex-col h-full border-slate-200">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
              <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <Icon name="lucide:alert-circle" class="w-4 h-4 text-rose-500" /> Tunggakan SPP
              </h3>
              <button @click="router.push('/finances/spp')" class="text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 px-2.5 py-1 rounded-md">Kelola</button>
            </div>
            <div class="p-0 flex-1 flex flex-col">
              <div v-if="dashboardData.spp_arrears.length === 0" class="p-8 text-center text-slate-400 text-sm m-auto">
                Semua SPP bulan ini lunas
              </div>
              <div v-else class="divide-y divide-slate-100">
                <div v-for="item in dashboardData.spp_arrears" :key="item.student_id" class="px-4 py-3 flex items-center justify-between hover:bg-rose-50/50 transition-colors">
                  <div class="min-w-0 flex-1 pr-3">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ item.student_name }}</p>
                    <p class="text-[10px] text-slate-500 truncate">{{ item.class_name }}</p>
                  </div>
                  <div class="text-right shrink-0">
                    <p class="text-sm font-black text-rose-600">{{ formatCurrency(item.amount) }}</p>
                    <p class="text-[10px] font-medium text-slate-400">{{ item.month }}</p>
                  </div>
                </div>
              </div>
            </div>
          </BaseCard>

          <!-- Top Savings -->
          <BaseCard class="p-0 flex flex-col h-full border-slate-200">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
              <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <Icon name="lucide:piggy-bank" class="w-4 h-4 text-blue-500" /> Tabungan Terbanyak
              </h3>
              <button @click="router.push('/finances/savings')" class="text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 px-2.5 py-1 rounded-md">Kelola</button>
            </div>
            <div class="p-0 flex-1 flex flex-col">
              <div v-if="dashboardData.top_savings.length === 0" class="p-8 text-center text-slate-400 text-sm m-auto">
                Belum ada data tabungan
              </div>
              <div v-else class="divide-y divide-slate-100">
                <div v-for="(item, i) in dashboardData.top_savings" :key="item.student_id" class="px-4 py-3 flex items-center justify-between hover:bg-slate-50 transition-colors">
                  <div class="flex items-center gap-3 min-w-0">
                    <div class="w-6 h-6 rounded-full bg-slate-100 text-[10px] font-black text-slate-500 flex items-center justify-center shrink-0">#{{ i + 1 }}</div>
                    <div class="min-w-0 flex-1 pr-3">
                      <p class="text-sm font-bold text-slate-800 truncate">{{ item.student_name }}</p>
                      <p class="text-[10px] text-slate-500 truncate">{{ item.class_name }}</p>
                    </div>
                  </div>
                  <div class="text-right shrink-0">
                    <p class="text-sm font-black text-blue-600">{{ formatCurrency(item.balance) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </BaseCard>
        </div>
      </template>
    </template>
  </div>
</template>

<style scoped lang="postcss">
.dashboard-action {
  @apply flex flex-col items-center justify-center p-4 rounded-2xl bg-surface border border-border-muted hover:border-primary-200 hover:shadow-lg hover:shadow-primary-600/5 transition-all gap-2 text-sm font-medium text-body;
}
.action-icon {
  @apply w-10 h-10 rounded-xl flex items-center justify-center;
}
</style>
