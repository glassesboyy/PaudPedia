<script setup lang="ts">
/**
 * SchoolSelectorLayout — Shown after login when a user has memberships.
 * Allows user to pick which school to access or to register a new one.
 */
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

const router = useRouter()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()

const memberships = computed(() => schoolStore.memberships)
const isLoading = computed(() => schoolStore.isLoading)

const roleLabels: Record<string, string> = {
  headmaster: 'Kepala Sekolah',
  teacher: 'Guru',
  parent: 'Orang Tua',
}

const roleColors: Record<string, string> = {
  headmaster: 'bg-primary-100 text-primary-700',
  teacher: 'bg-success-100 text-success-700',
  parent: 'bg-secondary-100 text-secondary-700',
}

function selectSchool(schoolId: number) {
  schoolStore.selectSchool(schoolId)
  router.push({ name: 'Dashboard' })
}

function handleLogout() {
  authStore.logout()
  router.push({ name: 'Login' })
}

onMounted(async () => {
  if (memberships.value.length === 0) {
    await schoolStore.fetchMemberships()
  }

  // Auto-select if only 1 school
  if (memberships.value.length === 1 && memberships.value[0]) {
    selectSchool(memberships.value[0].school_id)
  }
})
</script>

<template>
  <div class="min-h-screen bg-background flex items-center justify-center p-6">
    <div class="w-full max-w-lg animate-fade-in-up">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center mx-auto mb-4 shadow-medium">
          <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-heading">Pilih Sekolah</h1>
        <p class="mt-2 text-body">
          Halo, <span class="font-medium text-heading">{{ authStore.userName }}</span>! Pilih sekolah yang ingin Anda akses.
        </p>
      </div>

      <!-- Loading state -->
      <div v-if="isLoading" class="space-y-3">
        <div v-for="i in 2" :key="i" class="h-20 rounded-xl bg-surface-muted animate-pulse" />
      </div>

      <!-- Empty state -->
      <BaseCard v-else-if="memberships.length === 0" class="text-center">
        <div class="py-6">
          <svg class="w-12 h-12 text-muted mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <h3 class="text-lg font-semibold text-heading mb-2">Belum Ada Sekolah</h3>
          <p class="text-sm text-body mb-6">
            Anda belum terdaftar di sekolah manapun. Daftarkan sekolah Anda untuk mulai menggunakan SIAKAD.
          </p>
          <RouterLink to="/register">
            <BaseButton variant="primary">Daftarkan Sekolah</BaseButton>
          </RouterLink>
        </div>
      </BaseCard>

      <!-- School list -->
      <div v-else class="space-y-3">
        <button
          v-for="membership in memberships"
          :key="membership.school_id"
          class="w-full text-left card p-4 hover:border-primary-300 hover:shadow-medium transition-all duration-200 group cursor-pointer"
          @click="selectSchool(membership.school_id)"
        >
          <div class="flex items-center gap-4">
            <!-- School avatar -->
            <div class="w-12 h-12 rounded-xl bg-primary-50 group-hover:bg-primary-100 flex items-center justify-center flex-shrink-0 transition-colors">
              <span class="text-lg font-bold text-primary-600">
                {{ membership.school?.name?.charAt(0) || 'S' }}
              </span>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-semibold text-heading truncate group-hover:text-primary-600 transition-colors">
                {{ membership.school?.name }}
              </h3>
              <p class="text-xs text-muted mt-0.5">NPSN: {{ membership.school?.npsn }}</p>
            </div>

            <!-- Role badge -->
            <span :class="['text-xs font-medium px-2.5 py-1 rounded-full', roleColors[membership.role_type]]">
              {{ roleLabels[membership.role_type] || membership.role_type }}
            </span>

            <!-- Arrow -->
            <svg class="w-4 h-4 text-muted group-hover:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </button>
      </div>

      <!-- Footer actions -->
      <div class="mt-8 flex items-center justify-between">
        <button class="text-sm text-muted hover:text-danger-600 transition-colors" @click="handleLogout">
          Keluar
        </button>
        <RouterLink to="/register" class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
          + Daftarkan Sekolah Baru
        </RouterLink>
      </div>
    </div>
  </div>
</template>
