<script setup lang="ts">
/**
 * TheNavbar — Reusable navigation bar for all layouts.
 *
 * Props:
 *   showNav — Display the main navigation links (Kursus, Webinar, etc.).
 *             Typically true on public pages and false on dashboard pages.
 *
 * Slots:
 *   actions — Extra right-side actions (e.g. dashboard sidebar toggle).
 *
 * Auth-aware: renders Login/Register buttons for guests and a user
 * dropdown menu for authenticated users. Uses design system tokens
 * exclusively.
 */

const props = withDefaults(defineProps<{
  showNav?: boolean
}>(), {
  showNav: false,
})

const { user, isAuthenticated, isLoading, userName, logout } = useAuth()
const { itemCount } = useCart()
import { useSiteSettingsStore } from '~~/stores/siteSettings';
import { useAuthStore } from '~~/stores/auth';
const siteSettings = useSiteSettingsStore()
const authStore = useAuthStore()

const hasSchoolAccess = computed(() => authStore.hasSchoolAccess)
const siakadUrl = computed(() => authStore.siakadUrl)
const tokenCookie = useCookie('auth_token')

const isMobileMenuOpen = ref(false)
const isProfileOpen = ref(false)
const profileRef = ref<HTMLElement | null>(null)

const navItems = [
  { label: 'Kursus', to: '/courses' },
  { label: 'Produk', to: '/products' },
  { label: 'Webinar', to: '/webinars' },
  { label: 'Artikel', to: '/articles' },
  { label: 'Mentor', to: '/mentors' },
  { label: 'Siakad', to: '/siakad' },
]

async function handleLogout() {
  isProfileOpen.value = false
  isMobileMenuOpen.value = false
  await logout()
  await navigateTo('/')
}

function closeProfile() {
  isProfileOpen.value = false
}

function toggleProfile() {
  isProfileOpen.value = !isProfileOpen.value
}

// Close dropdown on outside click
function onClickOutside(event: MouseEvent) {
  if (profileRef.value && !profileRef.value.contains(event.target as Node)) {
    closeProfile()
  }
}

onMounted(() => {
  document.addEventListener('click', onClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', onClickOutside)
})
</script>

<template>
  <header class="sticky top-0 z-sticky border-b border-border bg-surface/95 backdrop-blur-sm">
    <div class="container py-3 flex items-center justify-between">
      <!-- Logo -->
      <NuxtLink to="/" class="text-2xl font-bold text-primary-600 shrink-0">
        {{ siteSettings.siteName }}
      </NuxtLink>

      <!-- Desktop nav links (only when showNav is true) -->
      <nav v-if="showNav" class="hidden md:flex items-center gap-1">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="px-3 py-2 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
        >
          {{ item.label }}
        </NuxtLink>
      </nav>

      <!-- Right side -->
      <div class="flex items-center gap-2">
        <!-- Cart icon -->
        <NuxtLink
          to="/cart"
          class="relative p-2 text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
        >
          <Icon name="lucide:shopping-cart" class="w-5 h-5" />
          <span
            v-if="itemCount > 0"
            class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-danger-500 text-white text-[10px] font-bold px-1"
          >
            {{ itemCount > 99 ? '99+' : itemCount }}
          </span>
        </NuxtLink>

        <!-- Desktop auth area -->
        <div class="hidden md:flex items-center gap-2">
          <!-- Loading skeleton -->
          <template v-if="isLoading">
            <div class="h-9 w-20 rounded-lg bg-surface-muted animate-pulse" />
          </template>

          <!-- Guest: Login + Register -->
          <template v-else-if="!isAuthenticated">
            <NuxtLink to="/auth/login">
              <UButton variant="ghost" size="sm">Masuk</UButton>
            </NuxtLink>
            <NuxtLink to="/auth/register">
              <UButton variant="primary" size="sm">Daftar</UButton>
            </NuxtLink>
          </template>

          <!-- Authenticated: user dropdown -->
          <template v-else>
            <div ref="profileRef" class="relative">
              <button
                type="button"
                class="flex items-center gap-2 p-1.5 rounded-full hover:bg-surface-muted transition-colors duration-200"
                @click.stop="toggleProfile"
              >
                <UAvatar
                  :src="user?.avatar_url"
                  :name="user?.name"
                  size="sm"
                />
                <span class="max-w-[120px] truncate text-sm font-medium text-body">
                  {{ userName }}
                </span>
                <Icon
                  name="lucide:chevron-down"
                  :class="[
                    'w-4 h-4 text-muted transition-transform duration-200',
                    isProfileOpen && 'rotate-180',
                  ]"
                />
              </button>

              <!-- Dropdown -->
              <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 scale-95 -translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 -translate-y-1"
              >
                <div
                  v-if="isProfileOpen"
                  class="absolute right-0 mt-2 w-56 bg-surface border border-border rounded-xl shadow-medium py-1 z-dropdown origin-top-right"
                >
                  <!-- User info -->
                  <div class="px-4 py-3 border-b border-border-muted">
                    <p class="text-sm font-semibold text-heading truncate">{{ user?.name }}</p>
                    <p class="text-xs text-muted truncate">{{ user?.email }}</p>
                  </div>

                  <!-- Actions -->
                  <div class="py-1">
                    <NuxtLink
                      to="/"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm text-body hover:bg-surface-muted transition-colors duration-200"
                      @click="closeProfile"
                    >
                      <Icon name="lucide:home" class="w-4 h-4" />
                      <span>Beranda</span>
                    </NuxtLink>
                    <NuxtLink
                      to="/account"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm text-body hover:bg-surface-muted transition-colors duration-200"
                      @click="closeProfile"
                    >
                      <Icon name="lucide:layout-dashboard" class="w-4 h-4" />
                      <span>Dashboard</span>
                    </NuxtLink>
                    <a
                      v-if="hasSchoolAccess"
                      :href="`${siakadUrl}/auth/token?token=${tokenCookie}`"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm text-primary-600 font-medium hover:bg-primary-50 transition-colors duration-200"
                      @click="closeProfile"
                    >
                      <Icon name="lucide:school" class="w-4 h-4" />
                      <span>Dashboard SIAKAD</span>
                    </a>
                    <NuxtLink
                      to="/account/settings"
                      class="flex items-center gap-3 px-4 py-2.5 text-sm text-body hover:bg-surface-muted transition-colors duration-200"
                      @click="closeProfile"
                    >
                      <Icon name="lucide:settings" class="w-4 h-4" />
                      <span>Pengaturan</span>
                    </NuxtLink>
                  </div>

                  <!-- Logout -->
                  <div class="border-t border-border-muted py-1">
                    <button
                      type="button"
                      class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-danger-600 hover:bg-danger-50 transition-colors duration-200"
                      @click="handleLogout"
                    >
                      <Icon name="lucide:log-out" class="w-4 h-4" />
                      <span>Keluar</span>
                    </button>
                  </div>
                </div>
              </Transition>
            </div>
          </template>
        </div>

        <!-- Slot for extra actions (e.g., dashboard sidebar toggle) -->
        <slot name="actions" />

        <!-- Mobile hamburger -->
        <button
          class="md:hidden p-2 text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
          @click="isMobileMenuOpen = !isMobileMenuOpen"
        >
          <Icon :name="isMobileMenuOpen ? 'lucide:x' : 'lucide:menu'" class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="isMobileMenuOpen"
        class="md:hidden border-t border-border bg-surface"
        @click="isMobileMenuOpen = false"
      >
        <nav class="container py-3 space-y-1">
          <!-- Nav links (only for public pages) -->
          <template v-if="showNav">
            <NuxtLink
              v-for="item in navItems"
              :key="item.to"
              :to="item.to"
              class="block px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
            >
              {{ item.label }}
            </NuxtLink>
          </template>

          <div class="pt-2 mt-2 border-t border-border-muted">
            <!-- Guest: Login + Register -->
            <template v-if="!isAuthenticated && !isLoading">
              <div class="flex gap-2 pt-1">
                <NuxtLink to="/auth/login" class="flex-1">
                  <UButton variant="outline" size="sm" block>Masuk</UButton>
                </NuxtLink>
                <NuxtLink to="/auth/register" class="flex-1">
                  <UButton variant="primary" size="sm" block>Daftar</UButton>
                </NuxtLink>
              </div>
            </template>

            <!-- Authenticated: quick links -->
            <template v-else-if="isAuthenticated">
              <NuxtLink
                to="/"
                class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
              >
                <Icon name="lucide:home" class="w-4 h-4" />
                Beranda
              </NuxtLink>
              <NuxtLink
                to="/account"
                class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
              >
                <Icon name="lucide:layout-dashboard" class="w-4 h-4" />
                Dashboard
              </NuxtLink>
              <a
                v-if="hasSchoolAccess"
                :href="`${siakadUrl}/auth/token?token=${tokenCookie}`"
                class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
              >
                <Icon name="lucide:school" class="w-4 h-4" />
                Dashboard SIAKAD
              </a>
              <NuxtLink
                to="/account/settings"
                class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
              >
                <Icon name="lucide:settings" class="w-4 h-4" />
                Pengaturan
              </NuxtLink>
              <button
                class="flex w-full items-center gap-2 px-3 py-2.5 text-sm font-medium text-danger-600 hover:bg-danger-50 rounded-lg transition-colors"
                @click="handleLogout"
              >
                <Icon name="lucide:log-out" class="w-4 h-4" />
                Keluar
              </button>
            </template>
          </div>
        </nav>
      </div>
    </Transition>
  </header>
</template>
