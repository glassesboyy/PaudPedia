<script setup lang="ts">
/**
 * Register Page — Tabbed registration for User Biasa / Sekolah.
 * Route: /auth/register
 *
 * Supports query param `?type=school` to auto-select the school tab.
 */
definePageMeta({
  layout: 'auth',
  middleware: ['guest'],
})

useSeo({ title: 'Daftar' })

const route = useRoute()
const activeTab = ref<'user' | 'school'>(
  route.query.type === 'school' ? 'school' : 'user'
)

// Keep tab in sync with URL
watch(activeTab, (val) => {
  const query = val === 'school' ? { type: 'school' } : {}
  navigateTo({ path: '/auth/register', query }, { replace: true })
})
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-center text-heading mb-6">Daftar Akun</h2>

    <!-- Tab Switcher -->
    <div class="flex rounded-xl bg-surface-muted p-1 mb-6">
      <button
        type="button"
        :class="[
          'flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200',
          activeTab === 'user'
            ? 'bg-surface text-heading shadow-sm'
            : 'text-muted hover:text-body',
        ]"
        @click="activeTab = 'user'"
      >
        User Biasa
      </button>
      <button
        type="button"
        :class="[
          'flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200',
          activeTab === 'school'
            ? 'bg-surface text-heading shadow-sm'
            : 'text-muted hover:text-body',
        ]"
        @click="activeTab = 'school'"
      >
        Sekolah
      </button>
    </div>

    <!-- Tab Content -->
    <RegisterForm v-if="activeTab === 'user'" />
    <RegisterSchoolForm v-else />
  </div>
</template>
