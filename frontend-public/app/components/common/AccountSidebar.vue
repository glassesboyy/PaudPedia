<script setup lang="ts">
/**
 * AccountSidebar — Sidebar navigation for account/dashboard pages.
 *
 * Shows user info header + navigation links to account sub-pages.
 * Uses design system tokens exclusively.
 */

import { useAuthStore } from '~~/stores/auth'

const route = useRoute()
const authStore = useAuthStore()

interface NavItem {
  label: string
  to: string
  icon: string
}

const navItems: NavItem[] = [
  { label: 'Profil Saya', to: '/account', icon: 'lucide:user' },
  { label: 'Kursus Saya', to: '/account/courses', icon: 'lucide:book-open' },
  { label: 'Produk Saya', to: '/account/products', icon: 'lucide:package' },
  { label: 'Webinar Saya', to: '/account/webinars', icon: 'lucide:video' },
  { label: 'Sertifikat Saya', to: '/account/certificates', icon: 'lucide:award' },
  { label: 'Riwayat Transaksi', to: '/account/orders', icon: 'lucide:receipt' },
  { label: 'Pengaturan', to: '/account/settings', icon: 'lucide:settings' },
]

function isActive(to: string): boolean {
  return route.path === to
}
</script>

<template>
  <div class="space-y-6">
    <!-- User info header -->
    <div class="flex items-center gap-3 px-3 py-2">
      <UAvatar
        :src="authStore.user?.avatar_url"
        :name="authStore.user?.name"
        size="lg"
      />
      <div class="min-w-0">
        <p class="text-sm font-semibold text-heading truncate">
          {{ authStore.user?.name }}
        </p>
        <p class="text-xs text-muted truncate">
          {{ authStore.user?.email }}
        </p>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="space-y-1">
      <NuxtLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        :class="[
          'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors duration-200',
          isActive(item.to)
            ? 'bg-primary-50 text-primary-600 font-semibold'
            : 'text-body hover:bg-surface-muted',
        ]"
      >
        <Icon :name="item.icon" class="w-5 h-5 shrink-0" />
        <span>{{ item.label }}</span>
      </NuxtLink>
    </nav>
  </div>
</template>
