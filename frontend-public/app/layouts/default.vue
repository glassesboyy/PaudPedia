<script setup lang="ts">
/**
 * Default Layout
 *
 * Used for all public pages (home, courses, articles, etc.)
 * Provides header, main content area, and footer.
 */
const { isAuthenticated, userName, isLoading, logout } = useAuth()

const isMenuOpen = ref(false)
const isProfileOpen = ref(false)

async function handleLogout() {
  await logout()
  await navigateTo('/auth/login')
}
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <header class="sticky top-0 z-sticky border-b border-border bg-surface/95 backdrop-blur-sm">
      <div class="container py-3 flex items-center justify-between">
        <!-- Logo -->
        <NuxtLink to="/" class="text-2xl font-bold text-primary-600 shrink-0">
          PaudPedia
        </NuxtLink>

        <!-- Desktop nav -->
        <nav class="hidden md:flex items-center gap-1">
          <NuxtLink
            to="/courses"
            class="px-3 py-2 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
          >
            Kursus
          </NuxtLink>
          <NuxtLink
            to="/webinars"
            class="px-3 py-2 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
          >
            Webinar
          </NuxtLink>
          <NuxtLink
            to="/articles"
            class="px-3 py-2 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
          >
            Artikel
          </NuxtLink>
          <NuxtLink
            to="/products"
            class="px-3 py-2 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
          >
            Produk
          </NuxtLink>
        </nav>

        <!-- Right side: auth buttons or profile -->
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

          <!-- Authenticated: profile dropdown -->
          <template v-else>
            <div class="relative">
              <button
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
                @click="isProfileOpen = !isProfileOpen"
              >
                <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center">
                  <Icon name="lucide:user" class="w-4 h-4 text-primary-600" />
                </div>
                <span class="max-w-[120px] truncate">{{ userName }}</span>
                <Icon
                  name="lucide:chevron-down"
                  class="w-4 h-4 transition-transform"
                  :class="isProfileOpen && 'rotate-180'"
                />
              </button>

              <!-- Dropdown -->
              <Transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
              >
                <div
                  v-if="isProfileOpen"
                  class="absolute right-0 mt-1 w-48 rounded-xl border border-border bg-surface shadow-medium py-1 z-dropdown"
                  @click="isProfileOpen = false"
                >
                  <NuxtLink
                    to="/account"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-body hover:text-foreground hover:bg-surface-muted transition-colors"
                  >
                    <Icon name="lucide:layout-dashboard" class="w-4 h-4" />
                    Dashboard
                  </NuxtLink>
                  <NuxtLink
                    to="/account/settings"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-body hover:text-foreground hover:bg-surface-muted transition-colors"
                  >
                    <Icon name="lucide:settings" class="w-4 h-4" />
                    Pengaturan
                  </NuxtLink>
                  <div class="my-1 border-t border-border-muted" />
                  <button
                    class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-danger-600 hover:bg-danger-50 transition-colors"
                    @click="handleLogout"
                  >
                    <Icon name="lucide:log-out" class="w-4 h-4" />
                    Keluar
                  </button>
                </div>
              </Transition>
            </div>
          </template>
        </div>

        <!-- Mobile hamburger -->
        <button
          class="md:hidden p-2 text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors"
          @click="isMenuOpen = !isMenuOpen"
        >
          <Icon :name="isMenuOpen ? 'lucide:x' : 'lucide:menu'" class="w-5 h-5" />
        </button>
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
        <div v-if="isMenuOpen" class="md:hidden border-t border-border bg-surface" @click="isMenuOpen = false">
          <nav class="container py-3 space-y-1">
            <NuxtLink to="/courses" class="block px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors">
              Kursus
            </NuxtLink>
            <NuxtLink to="/webinars" class="block px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors">
              Webinar
            </NuxtLink>
            <NuxtLink to="/articles" class="block px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors">
              Artikel
            </NuxtLink>
            <NuxtLink to="/products" class="block px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors">
              Produk
            </NuxtLink>

            <div class="pt-2 mt-2 border-t border-border-muted">
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
              <template v-else-if="isAuthenticated">
                <NuxtLink to="/account" class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors">
                  <Icon name="lucide:layout-dashboard" class="w-4 h-4" />
                  Dashboard
                </NuxtLink>
                <NuxtLink to="/account/settings" class="flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-body hover:text-foreground rounded-lg hover:bg-surface-muted transition-colors">
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

    <main class="flex-1">
      <slot />
    </main>

    <!-- TODO: <AppFooter /> -->
    <footer class="border-t border-border bg-surface-muted">
      <div class="container py-6 text-center text-sm text-muted">
        &copy; {{ new Date().getFullYear() }} PaudPedia. All rights reserved.
      </div>
    </footer>
  </div>
</template>