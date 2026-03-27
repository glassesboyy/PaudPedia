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
  await authStore.logout()
  router.push({ name: 'Login' })
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
  ]
  return items.filter((item) => item.roles.includes(role ?? ''))
})

const navIcons: Record<string, string> = {
  dashboard: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
  class: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
  teacher: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
  student: 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
  parent: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
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
        'fixed inset-y-0 left-0 z-modal w-64 bg-surface border-r border-border-muted transform transition-transform duration-200 ease-smooth lg:translate-x-0',
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center px-5 h-16 border-b border-border-muted">
            <span class="text-lg font-bold text-heading">PaudPedia</span>
            <span class="text-[10px] font-semibold text-primary-600 ml-1 bg-primary-50 px-1 py-0.5 rounded">SIAKAD</span>
        </div>

        <!-- School info -->
        <div class="px-4 py-3 border-b border-border-muted">
          <button class="w-full text-left group" @click="switchSchool">
            <p class="text-xs text-muted uppercase tracking-wide mb-0.5">Sekolah Aktif</p>
            <p class="text-sm font-semibold text-heading truncate group-hover:text-primary-600 transition-colors">
              {{ currentSchoolName }}
            </p>
            <p class="text-xs text-muted">{{ roleLabel }}</p>
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
          <RouterLink
            v-for="item in navItems"
            :key="item.name"
            :to="item.to"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-body no-underline hover:bg-surface-muted hover:text-heading transition-colors"
            active-class="!bg-primary-50 !text-primary-700"
            @click="isSidebarOpen = false"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" :d="navIcons[item.icon]" />
            </svg>
            {{ item.name }}
          </RouterLink>
        </nav>

        <!-- Sidebar footer -->
        <div class="px-3 py-4 border-t border-border-muted">
          <button
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-body hover:bg-danger-50 hover:text-danger-600 transition-colors w-full"
            @click="handleLogout"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Keluar
          </button>
        </div>
      </div>
    </aside>

    <!-- Main content area -->
    <div class="lg:pl-64">
      <!-- Topbar -->
      <header class="sticky top-0 z-sticky bg-surface/80 backdrop-blur-md border-b border-border-muted h-16 flex items-center px-4 sm:px-6">
        <!-- Mobile menu button -->
        <button
          class="lg:hidden p-2 -ml-2 rounded-lg text-body hover:bg-surface-muted transition-colors"
          @click="isSidebarOpen = !isSidebarOpen"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
