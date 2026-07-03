<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSchoolStore } from '@/stores/school.store'
import { operatorService } from '@/features/operators/services/operator.service'
import type { Operator } from '@/types'
import BaseButton from '@/components/ui/Button/Button.vue'
import BaseCard from '@/components/ui/Card/Card.vue'
import Skeleton from '@/components/ui/Skeleton/Skeleton.vue'
import ConfirmModal from '@/components/ui/Modal/ConfirmModal.vue'

const router = useRouter()
const route = useRoute()
const schoolStore = useSchoolStore()

const operatorId = computed(() => Number(route.params.id))
const isLoading = ref(true)
const operator = ref<Operator | null>(null)
const error = ref('')

const canManageOperators = computed(() => schoolStore.canManageOperators)

// Modal states
const showDeleteModal = ref(false)
const isDeleting = ref(false)
const showToggleModal = ref(false)
const isToggling = ref(false)

onMounted(async () => {
  if (schoolStore.currentSchoolId) {
    await fetchOperator()
  }
})

async function fetchOperator() {
  isLoading.value = true
  error.value = ''
  try {
    const response = await operatorService.getOperator(schoolStore.currentSchoolId!, operatorId.value)
    operator.value = response.data
  } catch {
    error.value = 'Gagal mengambil data operator sekolah.'
  } finally {
    isLoading.value = false
  }
}

async function executeDelete() {
  if (!operator.value) return
  isDeleting.value = true
  try {
    await operatorService.deleteOperator(schoolStore.currentSchoolId!, operator.value.id)
    router.push({ name: 'OperatorList' })
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal menghapus data operator.'
  } finally {
    isDeleting.value = false
    showDeleteModal.value = false
  }
}

async function executeToggle() {
  if (!operator.value) return
  isToggling.value = true
  try {
    await operatorService.toggleOperatorActive(schoolStore.currentSchoolId!, operator.value.id)
    await fetchOperator()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Gagal merubah status operator.'
  } finally {
    isToggling.value = false
    showToggleModal.value = false
  }
}

function formatDate(date?: string): string {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>

<template>
  <div class="max-w-4xl mx-auto animate-fade-in space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <button
          @click="router.push({ name: 'OperatorList' })"
          class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface hover:bg-surface-muted border border-border text-muted transition-colors shadow-sm"
        >
          <Icon name="lucide:arrow-left" class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-heading">Detail Operator Sekolah</h1>
          <p class="text-sm text-muted">Informasi lengkap staf operasional harian sekolah</p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div v-if="canManageOperators && operator" class="flex items-center gap-3 w-full sm:w-auto">
        <BaseButton
          :variant="operator.is_active ? 'outline' : 'primary'"
          :class="operator.is_active ? 'text-amber-600 border-amber-200 hover:bg-amber-50' : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
          @click="showToggleModal = true"
          class="w-full sm:w-auto"
        >
          <template #prepend>
            <Icon :name="operator.is_active ? 'lucide:power-off' : 'lucide:power'" class="w-4 h-4" />
          </template>
          {{ operator.is_active ? 'Nonaktifkan' : 'Aktifkan Akun' }}
        </BaseButton>
        <BaseButton
          variant="danger"
          @click="showDeleteModal = true"
          class="w-full sm:w-auto"
        >
          <template #prepend><Icon name="lucide:trash-2" class="w-4 h-4" /></template>
          Hapus
        </BaseButton>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="animate-fade-in space-y-6">
      <BaseCard class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
        <div class="p-8 bg-slate-50 border-b border-slate-100 flex items-center gap-6">
          <Skeleton width="6rem" height="6rem" class="rounded-2xl shrink-0" />
          <div class="space-y-3 w-full">
            <Skeleton width="40%" height="2rem" />
            <Skeleton width="180px" height="1.25rem" />
          </div>
        </div>
        <div class="p-8 space-y-6">
          <Skeleton width="100%" height="100px" class="rounded-2xl" />
        </div>
      </BaseCard>
    </div>

    <!-- Error View -->
    <div v-else-if="error && !operator" class="animate-fade-in py-12 px-6 text-center bg-white border border-slate-100 rounded-[2.5rem] shadow-xl shadow-primary-900/5 max-w-lg mx-auto">
      <div class="w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center text-rose-500 mx-auto mb-6 border border-rose-100 shadow-sm">
        <Icon name="lucide:alert-circle" class="w-10 h-10" />
      </div>
      <h3 class="text-xl font-black text-heading mb-2">Terjadi Kesalahan</h3>
      <p class="text-sm text-slate-500 font-medium mb-8 leading-relaxed">{{ error }}</p>
      <BaseButton variant="outline" @click="router.push({ name: 'OperatorList' })">
              <template #prepend><Icon name="lucide:x" class="w-4 h-4" /></template>
              Batal
            </BaseButton>
    </div>

    <!-- Content -->
    <BaseCard v-else-if="operator" class="p-0 border-none shadow-xl shadow-primary-900/5 overflow-hidden">
      <!-- Top Banner -->
      <div class="p-8 bg-gradient-to-br from-violet-600 to-indigo-700 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10">
          <Icon name="lucide:user-cog" class="w-64 h-64 text-white" />
        </div>
        <div class="flex items-center gap-6 relative z-10">
          <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white font-black text-3xl border border-white/30 shadow-inner shrink-0">
            {{ operator.name.charAt(0).toUpperCase() }}
          </div>
          <div class="space-y-1">
            <div class="flex items-center gap-3">
              <h2 class="text-2xl md:text-3xl font-black tracking-tight">{{ operator.name }}</h2>
              <span :class="[
                'px-3 py-0.5 rounded-full text-xs font-black uppercase tracking-wider border shadow-sm',
                operator.is_active 
                  ? 'bg-emerald-500 text-white border-emerald-400' 
                  : 'bg-rose-500 text-white border-rose-400'
              ]">
                {{ operator.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
            <p class="text-violet-100 font-medium text-sm flex items-center gap-2">
              <Icon name="lucide:mail" class="w-4 h-4" /> {{ operator.email }}
            </p>
          </div>
        </div>
        <div class="relative z-10 text-left md:text-right bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/20 w-full md:w-auto">
          <p class="text-[10px] uppercase font-black tracking-widest text-violet-200">Bergabung Sejak</p>
          <p class="text-base font-bold">{{ formatDate(operator.joined_at) }}</p>
        </div>
      </div>

      <div class="p-8 space-y-8">
        <!-- Informasi Detail -->
        <div class="space-y-6">
          <h3 class="text-lg font-black text-heading flex items-center gap-2 pb-3 border-b border-slate-100">
            <Icon name="lucide:user" class="w-5 h-5 text-violet-600" /> Informasi Kontak
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Lengkap</p>
              <p class="text-base font-bold text-slate-800">{{ operator.name }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alamat Email</p>
              <p class="text-base font-bold text-violet-600">{{ operator.email }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Telepon / WhatsApp</p>
              <p class="text-base font-bold text-slate-800">{{ operator.phone || '-' }}</p>
            </div>
            <div class="space-y-1.5">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Akun</p>
              <p class="text-base font-bold" :class="operator.is_active ? 'text-emerald-600' : 'text-rose-600'">
                {{ operator.is_active ? 'Akses Operasional Diizinkan' : 'Akses Operasional Diberhentikan' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Domain Wewenang & Akses card -->
        <div class="p-6 bg-violet-50/70 rounded-3xl border border-violet-100 space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-violet-600/20">
              <Icon name="lucide:shield-check" class="w-5 h-5" />
            </div>
            <div>
              <h4 class="text-base font-bold text-heading">Domain Wewenang Operasional & Akademik</h4>
              <p class="text-xs text-muted">Akses yang dimiliki oleh Operator Sekolah ini pada SIAKAD</p>
            </div>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
            <div class="bg-white p-3.5 rounded-xl border border-violet-100/80 flex items-center gap-3">
              <Icon name="lucide:check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0" />
              <span class="text-xs font-bold text-slate-700">Manajemen Administrasi Siswa (CRUD)</span>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-violet-100/80 flex items-center gap-3">
              <Icon name="lucide:check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0" />
              <span class="text-xs font-bold text-slate-700">Manajemen Pendidik / Guru (CRUD)</span>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-violet-100/80 flex items-center gap-3">
              <Icon name="lucide:check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0" />
              <span class="text-xs font-bold text-slate-700">Manajemen Ruang Kelas & Wali Kelas</span>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-violet-100/80 flex items-center gap-3">
              <Icon name="lucide:check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0" />
              <span class="text-xs font-bold text-slate-700">Manajemen Keuangan (SPP & Tabungan)</span>
            </div>
          </div>
          <p class="text-[11px] text-violet-700/80 italic pt-1">
            * Catatan: Operator tidak memiliki hak akses eksekutif seperti mengelola langganan Pro, transfer kepemilikan, atau menambah/menghapus Operator lain.
          </p>
        </div>
      </div>
    </BaseCard>

    <!-- Delete Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Operator Sekolah?"
      :message="`Anda akan menghapus operator '${operator?.name || ''}' dari lembaga sekolah ini. Hak aksesnya ke sekolah akan dicabut sepenuhnya.`"
      confirm-text="Ya, Hapus"
      variant="danger"
      :loading="isDeleting"
      @confirm="executeDelete"
      @cancel="showDeleteModal = false"
    />

    <!-- Toggle Status Modal -->
    <ConfirmModal
      :show="showToggleModal"
      :title="operator?.is_active ? 'Nonaktifkan Operator?' : 'Aktifkan Operator?'"
      :message="operator?.is_active 
        ? `Operator '${operator?.name}' tidak akan bisa mengakses dasbor sekolah selama statusnya nonaktif.` 
        : `Operator '${operator?.name}' akan diberikan kembali akses penuh ke dasbor operasional sekolah.`"
      :confirm-text="operator?.is_active ? 'Nonaktifkan' : 'Aktifkan'"
      :variant="operator?.is_active ? 'danger' : 'primary'"
      :loading="isToggling"
      @confirm="executeToggle"
      @cancel="showToggleModal = false"
    />
  </div>
</template>
