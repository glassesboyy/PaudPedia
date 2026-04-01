<script setup lang="ts">
/**
 * SchoolSelectorLayout — Shown after login when a user has memberships.
 * Allows user to pick which school to access or to register a new one.
 */
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

const router = useRouter()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()

const isLoggingOut = ref(false)
const memberships = computed(() => schoolStore.memberships)
const isLoading = computed(() => schoolStore.isLoading)

const roleLabels: Record<string, string> = {
  headmaster: 'Kepala Sekolah',
  teacher: 'Guru',
  parent: 'Orang Tua',
}

const roleColors: Record<string, string> = {
  headmaster: 'bg-primary-50 text-primary-700 ring-1 ring-primary-600/10',
  teacher: 'bg-slate-50 text-slate-700 ring-1 ring-slate-200',
  parent: 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/10',
}

function selectSchool(schoolId: number) {
  schoolStore.selectSchool(schoolId)
  router.push({ name: 'Dashboard' })
}

async function handleLogout() {
  isLoggingOut.value = true
  try {
    await authStore.logout()
    router.push({ name: 'Login' })
  } finally {
    isLoggingOut.value = false
  }
}

onMounted(async () => {
  if (memberships.value.length === 0) {
    await schoolStore.fetchMemberships()
  }

  // NOTE: Auto-selection removed to allow users to reach this screen 
  // via "Switch School" and see the "Register New School" link.
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Subtle Background Assets -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary-600/5 rounded-full blur-3xl -mr-64 -mt-64"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-slate-200/50 rounded-full blur-3xl -ml-64 -mb-64"></div>

    <div class="w-full max-w-lg animate-fade-in-up relative z-10">
      <!-- Header -->
      <div class="text-center mb-10">
        <div class="w-20 h-20 rounded-[2rem] bg-white shadow-xl shadow-primary-600/10 flex items-center justify-center mx-auto mb-8 border border-slate-100 group">
          <div class="w-14 h-14 rounded-2xl bg-primary-600 flex items-center justify-center transition-transform group-hover:scale-110">
            <Icon name="lucide:school" class="w-7 h-7 text-white" />
          </div>
        </div>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Pilih Unit Sekolah</h1>
        <p class="text-lg text-slate-500 font-medium">
          Halo, <span class="text-primary-600 underline decoration-primary-200 underline-offset-4">{{ authStore.userName }}</span>! Silahkan pilih unit sekolah yang terdaftar pada akun Anda dibawah ini!.
        </p>
      </div>

      <!-- Loading state -->
      <div v-if="isLoading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="h-24 rounded-2xl bg-white border border-slate-100 shadow-sm animate-pulse" />
      </div>

      <!-- Empty state -->
      <BaseCard v-else-if="memberships.length === 0" class="text-center p-2 border-none shadow-2xl shadow-slate-200/50 rounded-3xl">
        <div>
          <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <Icon name="lucide:building" class="w-10 h-10 text-slate-400" />
          </div>
          <h3 class="text-2xl font-black text-slate-900 mb-3">Unit Sekolah Tidak Ditemukan!</h3>
          <p class="text-slate-500 font-medium mb-6 leading-relaxed">
            Akun Anda belum terdaftar pada sekolah manapun di sistem SIAKAD. Silakan hubungi admin sekolah terkait atau pusat bantuan.
          </p>
          <BaseButton
            variant="primary"
            size="lg"
            class="w-full"
            :loading="isLoggingOut"
            @click="handleLogout"
          >
            Gunakan Akun Lain
          </BaseButton>
        </div>
      </BaseCard>

      <!-- School list -->
      <div v-else class="space-y-4">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 ml-2">Daftar Unit Tersedia</div>
        <button
          v-for="membership in memberships"
          :key="membership.school_id"
          class="w-full text-left bg-white p-5 rounded-2xl border border-slate-100 hover:border-primary-400 hover:shadow-xl hover:shadow-primary-600/5 transition-all duration-300 group relative overflow-hidden"
          @click="selectSchool(membership.school_id)"
        >
          <div class="flex items-center gap-5 relative z-10">
            <!-- School avatar -->
            <div class="w-14 h-14 rounded-xl bg-slate-50 group-hover:bg-primary-50 flex items-center justify-center flex-shrink-0 transition-colors border border-slate-100">
              <span class="text-xl font-black text-slate-400 group-hover:text-primary-600">
                {{ membership.school?.name?.charAt(0) || 'S' }}
              </span>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h3 class="text-base font-black text-slate-900 truncate group-hover:text-primary-600 transition-colors tracking-tight">
                {{ membership.school?.name }}
              </h3>
              <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">NPSN: {{ membership.school?.npsn }}</p>
            </div>

            <!-- Role badge -->
            <span :class="['text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-wider shadow-sm', roleColors[membership.role_type]]">
              {{ roleLabels[membership.role_type] || membership.role_type }}
            </span>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>
