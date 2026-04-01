<script setup lang="ts">
/**
 * DashboardLayout — Main app shell for authenticated views.
 * Includes sidebar, topbar, and main content area.
 *
 * This is a minimal working implementation that can be extended later
 * with full sidebar navigation, notifications, etc.
 */
import { ref, computed } from 'vue'
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

const navItems = computed(() => {
  const role = schoolStore.currentRole
  const items = [
    { name: 'Dashboard', icon: 'dashboard', to: '/', roles: ['headmaster', 'teacher', 'parent'] },
    { name: 'Kelas', icon: 'class', to: '/classes', roles: ['headmaster'] },
    { name: 'Guru', icon: 'teacher', to: '/teachers', roles: ['headmaster'] },
    { name: 'Siswa', icon: 'student', to: '/students', roles: ['headmaster', 'teacher'] },
    { name: 'Orang Tua', icon: 'parent', to: '/parents', roles: ['headmaster'] },
    { name: 'Absensi', icon: 'attendance', to: '/attendance', roles: ['headmaster', 'teacher'] },
    { name: 'Anak Saya', icon: 'child', to: '/children', roles: ['parent'] },
    { name: 'Pengaturan Sekolah', icon: 'school', to: '/school/profile', roles: ['headmaster'] },
  ]
  return items.filter((item) => item.roles.includes(role ?? ''))
})

const navIcons: Record<string, string> = {
  dashboard: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
  school: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z',
  class: 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
  teacher: 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
  student: 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5',
  parent: 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
  attendance: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
  child: 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
}
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
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                </svg>
              </div>
              <div class="flex-1 min-w-0 text-left">
                <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">Unit Aktif</p>
                <p class="text-sm font-bold text-slate-900 truncate leading-tight">{{ currentSchoolName }}</p>
                <p class="text-[10px] text-primary-600 font-bold uppercase">{{ roleLabel }}</p>
              </div>
              <svg class="w-4 h-4 text-slate-300 group-hover:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-4 py-2 space-y-1">
          <RouterLink
            v-for="item in navItems"
            :key="item.name"
            :to="item.to"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-slate-600 no-underline hover:bg-slate-50 hover:text-slate-900 transition-all duration-200 group border border-transparent"
            active-class="!bg-primary-600 !text-white !border-primary-500 shadow-sm"
            @click="isSidebarOpen = false"
          >
            <svg 
              class="w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor" 
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" :d="navIcons[item.icon]" />
            </svg>
            {{ item.name }}
          </RouterLink>
        </nav>

        <!-- Sidebar footer -->
        <div class="p-4 border-t border-border/50">
          <button
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-danger-600 hover:bg-danger-50 hover:scale-[0.98] transition-all duration-200 w-full disabled:opacity-50 disabled:pointer-events-none"
            :disabled="isLoggingOut"
            @click="handleLogout"
          >
            <svg v-if="!isLoggingOut" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <svg v-else class="animate-spin h-5 w-5 text-danger-600 flex-shrink-0" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
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
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
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
      <main class="p-4 sm:p-6 lg:p-8">
        <RouterView />
      </main>
    </div>
  </div>
</template>
