<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { http } from '@/services/http/client'
import { useAuthStore } from '@/stores/auth.store'
import { useSchoolStore } from '@/stores/school.store'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const schoolStore = useSchoolStore()

const token = route.params.token as string
const isLoading = ref(true)
const isProcessing = ref(false)
const error = ref('')
const success = ref(false)
const requestDetails = ref<any>(null)

onMounted(async () => {
  try {
    const res = await http.get(`/api/v1/schools/transfer/accept/${token}`)
    requestDetails.value = res

    if (authStore.user?.email !== requestDetails.value.to_email) {
      error.value = 'Anda tidak berhak melihat atau menerima undangan ini. Silakan login dengan email yang benar.'
    }
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Undangan tidak valid atau sudah kedaluwarsa.'
  } finally {
    isLoading.value = false
  }
})

async function handleAccept() {
  isProcessing.value = true
  error.value = ''
  
  try {
    await http.post(`/api/v1/schools/transfer/accept/${token}`)
    success.value = true
    await authStore.fetchUser() // Refresh user roles
    await schoolStore.fetchMemberships() // Refresh schools
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal menerima undangan.'
  } finally {
    isProcessing.value = false
  }
}

async function handleReject() {
  isProcessing.value = true
  error.value = ''
  
  try {
    await http.post(`/api/v1/schools/transfer/reject/${token}`)
    error.value = 'Undangan berhasil ditolak.'
    requestDetails.value = null
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal menolak undangan.'
  } finally {
    isProcessing.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <BaseCard class="max-w-md w-full shadow-2xl shadow-primary-900/5">
      <div class="p-8 space-y-6">
        <div class="text-center">
          <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <Icon name="lucide:building" class="w-8 h-8 text-primary-600" />
          </div>
          <h1 class="text-2xl font-black text-slate-900">Transfer Kepemilikan</h1>
          <p class="text-slate-500 mt-2">Undangan Menjadi Kepala Sekolah</p>
        </div>

        <div v-if="isLoading" class="text-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
          <p class="mt-4 text-sm text-slate-500">Memuat detail undangan...</p>
        </div>

        <div v-else-if="success" class="text-center py-6 space-y-6">
          <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
            <Icon name="lucide:check" class="w-8 h-8 text-emerald-600" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-900">Selamat!</h3>
            <p class="text-slate-600 mt-2">Anda sekarang adalah Kepala Sekolah resmi untuk <strong>{{ requestDetails?.school?.name }}</strong>.</p>
          </div>
          <BaseButton variant="primary" class="w-full" @click="router.push({ name: 'Dashboard' })">
            Masuk ke Dashboard
          </BaseButton>
        </div>

        <div v-else-if="error" class="bg-red-50 text-red-700 p-4 rounded-xl text-center border border-red-100">
          <Icon name="lucide:alert-circle" class="w-6 h-6 mx-auto mb-2" />
          <p class="text-sm font-medium">{{ error }}</p>
          <BaseButton v-if="!requestDetails" variant="outline" class="mt-4 w-full" @click="router.push({ name: 'Dashboard' })">
            Kembali ke Beranda
          </BaseButton>
        </div>

        <div v-else-if="requestDetails" class="space-y-6">
          <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-xs font-bold text-slate-400 uppercase">Sekolah</span>
              <span class="text-sm font-bold text-slate-900">{{ requestDetails.school.name }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs font-bold text-slate-400 uppercase">Dari</span>
              <span class="text-sm font-bold text-slate-900">{{ requestDetails.from_user.name }}</span>
            </div>
          </div>

          <p class="text-sm text-slate-600 text-center">
            Dengan menerima undangan ini, Anda akan memegang kendali penuh atas manajemen, keuangan, dan data sekolah ini.
          </p>

          <div class="grid grid-cols-2 gap-3">
            <BaseButton variant="outline" :loading="isProcessing" @click="handleReject">
              Tolak
            </BaseButton>
            <BaseButton variant="primary" :loading="isProcessing" @click="handleAccept">
              Terima Jabatan
            </BaseButton>
          </div>
        </div>
      </div>
    </BaseCard>
  </div>
</template>
