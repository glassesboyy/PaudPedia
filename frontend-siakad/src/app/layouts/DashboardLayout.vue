<script setup lang="ts">
/**
 * DashboardLayout — Main app shell for authenticated views.
 * Includes sidebar, topbar, and main content area.
 *
 * This is a minimal working implementation that can be extended later
 * with full sidebar navigation, notifications, etc.
 */
import { ref, onMounted, computed } from 'vue'
import { RouterView, RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseButton from '@/components/ui/Button/Button.vue'

const router = useRouter()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()

const isSidebarOpen = ref(false)
const isLoggingOut = ref(false)

const currentSchoolName = computed(() => schoolStore.currentSchool?.name ?? 'Sekolah')

const roleLabel = computed(() => {
  const labels: Record<string, string> = {
    headmaster: 'Kepala Sekolah',
    operator: 'Operator Sekolah',
    teacher: 'Guru',
    parent: 'Orang Tua',
  }
  return labels[schoolStore.currentRole ?? ''] ?? ''
})

async function handleLogout() {
  isLoggingOut.value = true
  try {
    await authStore.logout()
    router.push({ name: 'Login' })
  } finally {
    isLoggingOut.value = false
  }
}

function switchSchool() {
  schoolStore.clearSchool()
  router.push({ name: 'SelectSchool' })
}

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await schoolStore.fetchSubscriptionInfo()
  }
})

const navItems = computed(() => {
  const role = schoolStore.currentRole
  const items = [
    // Dashboard & Profil
    { name: 'Dashboard', icon: 'dashboard', to: '/', roles: ['headmaster', 'operator', 'teacher', 'parent'], isPro: false },
    { name: 'Profil Sekolah', icon: 'school', to: '/school/profile', roles: ['headmaster', 'operator', 'teacher', 'parent'], isPro: false },
    
    // Kelembagaan & Staf
    { name: 'Manajemen Operator', icon: 'operator', to: '/operators', roles: ['headmaster'], isPro: false },
    { name: role === 'headmaster' ? 'Monitoring Guru' : 'Manajemen Guru', icon: 'teacher', to: '/teachers', roles: ['headmaster', 'operator'], isPro: false },
    
    // Akademik & Siswa
    { name: role === 'teacher' ? 'Kelas Saya' : (role === 'headmaster' ? 'Monitoring Kelas' : 'Manajemen Kelas'), icon: 'class', to: '/classes', roles: ['headmaster', 'operator', 'teacher'], isPro: false },
    { name: role === 'teacher' ? 'Data Siswa' : (role === 'headmaster' ? 'Monitoring Siswa' : 'Manajemen Siswa'), icon: 'student', to: '/students', roles: ['headmaster', 'operator', 'teacher'], isPro: false },
    { name: role === 'teacher' ? 'Data Wali Murid' : (role === 'headmaster' ? 'Monitoring Wali Murid' : 'Manajemen Wali Murid'), icon: 'parent', to: '/parents', roles: ['headmaster', 'operator', 'teacher'], isPro: false },
    { name: 'Anak Saya', icon: 'child', to: '/children', roles: ['parent'], isPro: false },
    
    // Kehadiran & Penilaian
    { name: role === 'teacher' ? 'Presensi Kelas' : 'Monitoring Presensi', icon: 'attendance', to: '/attendance', roles: ['headmaster', 'teacher'], isPro: false },
    { name: 'Master Penilaian', icon: 'settings', to: '/assessments/settings', roles: ['operator'], isPro: false },
    { name: role === 'teacher' ? 'Input Penilaian' : 'Monitoring Penilaian', icon: 'assessment', to: '/assessments', roles: ['headmaster', 'teacher'], isPro: false },
    { name: 'Panduan Penilaian', icon: 'info', to: '/assessments/guide', roles: ['headmaster', 'operator', 'teacher', 'parent'], isPro: false },
    { name: 'Rapor Naratif', icon: 'report_builder', to: '/assessments/report', roles: ['headmaster', 'teacher'], isPro: true },
    { name: 'Cetak Rapor', icon: 'report', to: '/reports', roles: ['headmaster', 'teacher'], isPro: true },

    // Keuangan (Pro Domain)
    { name: role === 'teacher' ? 'Tagihan SPP' : (role === 'headmaster' ? 'Monitoring SPP' : 'Manajemen SPP'), icon: 'finance', to: '/finances/spp', roles: ['headmaster', 'operator', 'teacher'], isPro: true },
    { name: role === 'teacher' ? 'Tabungan Siswa' : (role === 'headmaster' ? 'Monitoring Tabungan' : 'Manajemen Tabungan'), icon: 'savings', to: '/finances/savings', roles: ['headmaster', 'operator', 'teacher'], isPro: true },
  ]
  return items.filter((item) => {
    if (!item.roles.includes(role ?? '')) return false
    if (item.isPro && !schoolStore.isPro) return false
    return true
  })
})

const navIcons: Record<string, string> = {
  dashboard: 'lucide:layout-dashboard',
  operator: 'lucide:user-cog',
  class: 'lucide:users',
  teacher: 'lucide:graduation-cap',
  student: 'lucide:backpack',
  parent: 'lucide:shield',
  attendance: 'lucide:calendar-check',
  child: 'lucide:baby',
  assessment: 'lucide:bar-chart-2',
  school: 'lucide:settings',
  finance: 'lucide:wallet',
  savings: 'lucide:piggy-bank',
  report: 'lucide:file-text',
  report_builder: 'lucide:book-open',
  subscription: 'lucide:crown',
  settings: 'lucide:settings-2',
  info: 'lucide:info',
}

const freePlanItems = computed(() => navItems.value.filter(item => !item.isPro))
const proPlanItems = computed(() => navItems.value.filter(item => item.isPro))

const subscriptionItem = computed(() => {
  if (schoolStore.currentRole !== 'headmaster') return null
  return { name: 'Paket Langganan', icon: 'subscription', to: '/school/subscription' }
})

import { useRoute } from 'vue-router'
const route = useRoute()

const activeItemTo = computed(() => {
  const allItems = [...freePlanItems.value, ...proPlanItems.value]
  if (subscriptionItem.value) allItems.push(subscriptionItem.value as any)
  
  const matchingItems = allItems.filter(item => route.path === item.to || (item.to !== '/' && route.path.startsWith(item.to + '/')))
  if (matchingItems.length === 0) {
    const fallbackMatch = allItems.filter(item => item.to !== '/' && route.path.startsWith(item.to))
    if (fallbackMatch.length > 0) {
      fallbackMatch.sort((a, b) => b.to.length - a.to.length)
      return fallbackMatch[0]!.to
    }
    return null
  }
  matchingItems.sort((a, b) => b.to.length - a.to.length)
  return matchingItems[0]!.to
})
</script>

<template>
  <div class="min-h-screen bg-background">
    <!-- Mobile sidebar overlay -->
    <div
      v-if="isSidebarOpen"
      class="fixed inset-0 bg-black/50 z-overlay lg:hidden"
      @click="isSidebarOpen = false"
    />

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-modal w-72 bg-surface border-r border-border transform transition-transform duration-300 ease-smooth lg:translate-x-0 shadow-xl lg:shadow-none',
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center px-6 h-20 border-b border-slate-100">
          <div class="flex items-center gap-1.5 group cursor-pointer" @click="router.push('/')">
            <span class="text-xl font-black text-slate-900 tracking-tighter group-hover:text-primary-600 transition-colors">
              PaudPedia<span class="text-primary-600 ml-1 font-bold text-xs uppercase tracking-[0.2em] relative -top-0.5">Siakad</span>
            </span>
          </div>
        </div>

        <!-- School info -->
        <div class="px-4 py-6">
          <button 
            @click="switchSchool"
            class="w-full p-3.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-primary-400 hover:bg-white transition-all duration-200 group"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 group-hover:text-primary-600 group-hover:border-primary-200 transition-all">
                <Icon name="lucide:building" class="w-5 h-5" />
              </div>
              <div class="flex-1 min-w-0 text-left">
                <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">Unit Aktif</p>
                <p class="text-sm font-bold text-slate-900 truncate leading-tight">{{ currentSchoolName }}</p>
                <p class="text-[10px] text-primary-600 font-bold uppercase">{{ roleLabel }}</p>
              </div>
              <Icon name="lucide:chevron-down" class="w-4 h-4 text-slate-300 group-hover:text-primary-500 transition-colors" />
            </div>
          </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-6">
          
          <!-- SUBSCRIPTION SETTINGS (TOP) -->
          <div v-if="subscriptionItem" class="pb-4 mb-4 border-b border-slate-100">
            <RouterLink
              :to="subscriptionItem.to"
              :class="[
                'flex items-center justify-between px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group border shadow-sm relative overflow-hidden',
                activeItemTo === subscriptionItem.to
                  ? 'bg-violet-600 text-white hover:text-white border-violet-600 shadow-violet-600/20'
                  : 'bg-violet-50 text-violet-700 border-violet-100 hover:bg-violet-100 hover:text-violet-900 hover:border-violet-200'
              ]"
              @click="isSidebarOpen = false"
            >
              <div class="flex items-center gap-3">
                <Icon 
                  :name="navIcons[subscriptionItem.icon]"
                  :class="['w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110', activeItemTo === subscriptionItem.to ? 'text-white' : 'text-violet-600 group-hover:text-violet-800']" 
                />
                <span :class="activeItemTo === subscriptionItem.to ? 'group-hover:text-white text-white' : 'group-hover:text-violet-900'">{{ subscriptionItem.name }}</span>
              </div>
            </RouterLink>
          </div>

          <!-- FREE PLAN -->
          <div>
            <p class="px-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Umum</p>
            <div class="space-y-1">
              <RouterLink
                v-for="item in freePlanItems"
                :key="item.name"
                :to="item.to"
                :class="[
                  'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group border',
                  activeItemTo === item.to
                    ? 'bg-primary-50 text-primary-700 border-primary-200 shadow-sm'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-transparent'
                ]"
                @click="isSidebarOpen = false"
              >
                <Icon 
                  :name="navIcons[item.icon]"
                  :class="['w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110', activeItemTo === item.to ? 'text-primary-600' : 'text-slate-400']" 
                />
                {{ item.name }}
              </RouterLink>
            </div>
          </div>

          <!-- PRO PLAN -->
          <div v-if="proPlanItems.length > 0">
            <div class="flex items-center gap-2 px-4 mb-2">
              <p class="text-[10px] font-extrabold text-amber-500 uppercase tracking-widest">PRO PLAN</p>
              <Icon name="lucide:crown" class="w-3 h-3 text-amber-500" />
            </div>
            <div class="space-y-1">
              <RouterLink
                v-for="item in proPlanItems"
                :key="item.name"
                :to="item.to"
                :class="[
                  'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group border',
                  activeItemTo === item.to
                    ? 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-transparent'
                ]"
                @click="isSidebarOpen = false"
              >
                <Icon 
                  :name="navIcons[item.icon]"
                  :class="['w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110', activeItemTo === item.to ? 'text-amber-600' : 'text-slate-400']" 
                />
                {{ item.name }}
              </RouterLink>
            </div>
          </div>

        </nav>

        <!-- Sidebar footer -->
        <div class="p-4 border-t border-border/50">
          <button
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-danger-600 hover:bg-danger-50 hover:scale-[0.98] transition-all duration-200 w-full disabled:opacity-50 disabled:pointer-events-none"
            :disabled="isLoggingOut"
            @click="handleLogout"
          >
            <Icon v-if="!isLoggingOut" name="lucide:log-out" class="w-5 h-5 flex-shrink-0" />
            <Icon v-else name="lucide:loader-2" class="animate-spin h-5 w-5 text-danger-600 flex-shrink-0" />
            {{ isLoggingOut ? 'Sedang Keluar...' : 'Keluar Akun' }}
          </button>
        </div>
      </div>
    </aside>

    <!-- Main content area -->
    <div class="lg:pl-72">
      <!-- Topbar -->
      <header class="sticky top-0 z-sticky bg-surface/80 backdrop-blur-md border-b border-border/50 h-20 flex items-center px-6 sm:px-10">
        <!-- Mobile menu button -->
        <button
          class="lg:hidden p-2.5 -ml-2 rounded-xl bg-surface-muted text-heading hover:bg-primary-50 hover:text-primary-600 transition-all"
          @click="isSidebarOpen = !isSidebarOpen"
        >
          <Icon name="lucide:menu" class="w-6 h-6" />
        </button>

        <div class="flex-1" />

        <!-- User menu -->
        <div class="flex items-center gap-3">
          <div class="text-right hidden sm:block">
            <p class="text-sm font-medium text-heading">{{ authStore.userName }}</p>
            <p class="text-xs text-muted">{{ roleLabel }}</p>
          </div>
          <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center">
            <span class="text-sm font-semibold text-primary-700">
              {{ authStore.userName.charAt(0).toUpperCase() }}
            </span>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Core Lock Banner -->
        <div v-if="schoolStore.isCoreLocked" class="bg-danger-50 border-2 border-danger-500 rounded-2xl p-6 md:p-8 shadow-xl shadow-danger-900/10 relative overflow-hidden">
          <div class="absolute -right-10 -bottom-10 opacity-5">
            <Icon name="lucide:lock" class="w-64 h-64 text-danger-900" />
          </div>
          <div class="relative z-10 flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
            <div>
              <h2 class="text-xl md:text-2xl font-black text-danger-700 flex items-center gap-3 mb-2">
                <Icon name="lucide:lock" class="w-6 h-6" /> Akses Terkunci
              </h2>
              <p class="text-danger-600 font-medium max-w-3xl leading-relaxed">
                Jumlah data Anda (Siswa: {{ schoolStore.subscriptionInfo?.student_usage }}/{{ schoolStore.subscriptionInfo?.student_limit }}, Guru: {{ schoolStore.subscriptionInfo?.teacher_usage }}/{{ schoolStore.subscriptionInfo?.teacher_limit }}) melebihi batas Paket Gratis. Untuk menggunakan seluruh fitur SIAKAD kembali secara normal, Anda harus Upgrade ke Paket Pro atau menghapus kelebihan data.
              </p>
            </div>
            <BaseButton variant="danger" size="lg" @click="$router.push('/school/subscription')" class="whitespace-nowrap shadow-lg shadow-danger-500/30">
              <template #prepend><Icon name="lucide:crown" class="w-5 h-5" /></template>
              Upgrade Sekarang
            </BaseButton>
          </div>
        </div>

        <!-- H-3 Warning Banner (Danger/Red) -->
        <div v-else-if="schoolStore.isHeadmaster && schoolStore.daysUntilExpiration !== null && schoolStore.daysUntilExpiration <= 3" class="bg-danger-50 border-2 border-danger-500 rounded-xl p-4 flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <Icon name="lucide:alert-triangle" class="w-6 h-6 text-danger-600 flex-shrink-0" />
            <p class="text-danger-800 font-medium">Masa aktif paket Pro Anda tinggal <strong class="text-danger-900 font-black">{{ schoolStore.daysUntilExpiration }} hari</strong> lagi. Jangan sampai fitur terkunci, segera lakukan perpanjangan!</p>
          </div>
          <button v-if="$route.path !== '/school/subscription'" class="bg-danger-600 hover:bg-danger-700 text-white font-bold py-1.5 px-4 rounded-lg text-sm transition-colors shadow-sm whitespace-nowrap" @click="$router.push('/school/subscription')">Perpanjang</button>
        </div>

        <!-- H-7 Warning Banner (Amber/Yellow) -->
        <div v-else-if="schoolStore.isHeadmaster && schoolStore.daysUntilExpiration !== null && schoolStore.daysUntilExpiration <= 7" class="bg-amber-50 border-2 border-amber-500 rounded-xl p-4 flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <Icon name="lucide:alert-circle" class="w-6 h-6 text-amber-600 flex-shrink-0" />
            <p class="text-amber-800 font-medium">Masa aktif paket Pro Anda tinggal <strong class="text-amber-900 font-black">{{ schoolStore.daysUntilExpiration }} hari</strong> lagi. Jangan sampai fitur terkunci, segera lakukan perpanjangan!</p>
          </div>
          <button v-if="$route.path !== '/school/subscription'" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-1.5 px-4 rounded-lg text-sm transition-colors shadow-sm whitespace-nowrap" @click="$router.push('/school/subscription')">Perpanjang</button>
        </div>

        <RouterView />
      </main>
    </div>
  </div>
</template>
